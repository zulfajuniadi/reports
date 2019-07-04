    SELECT organization_units_4.name AS ou1_name,
        organization_units_3.name AS ou2_name,
        organization_units_2.name AS ou3_name,
        organization_units_1.name AS ou4_name,
        organization_units.name AS ou5_name,
        { fn CONCAT(dbo.users.first_name, dbo.users.last_name) } AS full_name,
        dbo.users.username,
        organization_units_4.id AS ou1_id,
        organization_units_3.id AS ou2_id,
        organization_units_2.id AS ou3_id,
        organization_units_1.id AS ou4_id,
        organization_units.id AS ou5_id,
        organization_units.depth
    FROM dbo.users INNER JOIN
        dbo.organization_units ON dbo.users.organization_unit_id = dbo.organization_units.id INNER JOIN
        dbo.organization_units AS organization_units_1 ON dbo.organization_units.parent_id = organization_units_1.id INNER JOIN
        dbo.organization_units AS organization_units_2 ON organization_units_1.parent_id = organization_units_2.id INNER JOIN
        dbo.organization_units AS organization_units_3 ON organization_units_2.parent_id = organization_units_3.id inner join
        dbo.organization_units AS organization_units_4 ON organization_units_3.parent_id = organization_units_4.id
    WHERE  organization_units.depth = 4

union all

    SELECT organization_units_3.name AS ou1_name,
        organization_units_2.name AS ou2_name,
        organization_units_1.name AS ou3_name,
        dbo.organization_units.name AS ou4_name,
        null AS ou5_name,
        { fn CONCAT(dbo.users.first_name, dbo.users.last_name) } AS full_name,
        dbo.users.username,
        organization_units_3.id AS ou1_id,
        organization_units_2.id AS ou2_id,
        organization_units_1.id AS ou3_id,
        dbo.organization_units.id AS ou4_id,
        null AS ou5_id,
        organization_units.depth
    FROM dbo.users INNER JOIN
        dbo.organization_units ON dbo.users.organization_unit_id = dbo.organization_units.id INNER JOIN
        dbo.organization_units AS organization_units_1 ON dbo.organization_units.parent_id = organization_units_1.id INNER JOIN
        dbo.organization_units AS organization_units_2 ON organization_units_1.parent_id = organization_units_2.id INNER JOIN
        dbo.organization_units AS organization_units_3 ON organization_units_2.parent_id = organization_units_3.id
    WHERE  organization_units.depth = 3

union all

    SELECT organization_units_2.name AS ou1_name,
        organization_units_1.name AS ou2_name,
        dbo.organization_units.name AS ou3_name,
        NULL AS ou4_name,
        NULL AS ou5_name,
        { fn CONCAT(dbo.users.first_name,dbo.users.last_name) } AS full_name,
        dbo.users.username,
        organization_units_2.id AS ou1_id,
        organization_units_1.id AS ou2_id,
        organization_units.id AS ou3_id,
        NULL AS ou4_id,
        NULL AS ou5_id,
        organization_units.depth
    FROM dbo.users INNER JOIN
        dbo.organization_units ON dbo.users.organization_unit_id = dbo.organization_units.id INNER JOIN
        dbo.organization_units AS organization_units_1 ON dbo.organization_units.parent_id = organization_units_1.id INNER JOIN
        dbo.organization_units AS organization_units_2 ON organization_units_1.parent_id = organization_units_2.id
    WHERE  organization_units.depth = 2

union all

    SELECT organization_units_1.name AS ou1_name,
        organization_units.name AS ou2_name,
        null AS ou3_name,
        NULL AS ou4_name,
        NULL AS ou5_name,
        { fn CONCAT(dbo.users.first_name,dbo.users.last_name) } AS full_name,
        dbo.users.username,
        organization_units_1.id AS ou1_id,
        organization_units.id AS ou2_id,
        null AS ou3_id,
        NULL AS ou4_id,
        NULL AS ou5_id,
        organization_units.depth
    FROM dbo.users INNER JOIN
        dbo.organization_units ON dbo.users.organization_unit_id = dbo.organization_units.id INNER JOIN
        dbo.organization_units AS organization_units_1 ON dbo.organization_units.parent_id = organization_units_1.id
    WHERE  organization_units.depth = 1

union all

    SELECT organization_units.name AS ou1_name,
        null AS ou2_name,
        null AS ou3_name,
        NULL AS ou4_name,
        NULL AS ou5_name,
        { fn CONCAT(dbo.users.first_name,dbo.users.last_name) } AS full_name,
        dbo.users.username,
        organization_units.id AS ou1_id,
        null AS ou2_id,
        null AS ou3_id,
        NULL AS ou4_id,
        NULL AS ou5_id,
        organization_units.depth
    FROM dbo.users INNER JOIN
        dbo.organization_units ON dbo.users.organization_unit_id = dbo.organization_units.id
    WHERE  organization_units.depth = 0