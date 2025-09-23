create or replace view v_menu_permissions as
WITH permission AS (SELECT permissions.name,
                           role_has_permissions.role_id,
                           model_has_roles.model_id,
                           roles.tenant_id
                    FROM permissions
                             JOIN role_has_permissions ON permissions.id = role_has_permissions.permission_id
                             JOIN model_has_roles ON role_has_permissions.role_id = model_has_roles.role_id
                             JOIN roles ON roles.id = role_has_permissions.role_id
                    WHERE model_has_roles.model_type::text = 'App\Models\User'::text)
SELECT distinct menus.id,
                case
                    when lower(menus.name) !~ lower(customer) then replace(menus.name, 'Patient', customer)
                    else menus.name
                    end
                                                      as name,
                menus.route,
                menus.params,
                coalesce(root.id, menus.id)           AS root_id,
                case
                    when lower(root.name) !~ lower(customer) then replace(root.name, 'Patient', customer)
                    else coalesce(root.name, menus.name)
                    end
                                                      AS root_name,

                case
                    when root.route is null and menus.parent_id is null then menus.route
                    else root.route
                    end                               AS root_route,
                coalesce(root.params, menus.params)   AS root_params,
                coalesce(root."order", menus."order") AS root_order,
                case
                    when root.id is null then 0
                    else menus."order"
                    end
                                                      as order,
                coalesce(root.icon, menus.icon)       AS root_icon,
                permission.role_id,
                permission.model_id,
                permission.tenant_id
FROM menus
         JOIN permission ON permission.name::text = menus.route::text OR
                            (menus.parent_id is null and menus.route::text = replace(permission.name::text, substring(permission.name::text, strpos(permission.name::text, '.') + 1), 'index'))

         LEFT JOIN menus root ON root.id = menus.parent_id AND root.status = true
         JOIN accounts on accounts.tenant_id = permission.tenant_id
WHERE menus.status = true
ORDER BY 5, 9;