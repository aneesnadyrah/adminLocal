<?php

class ProjectInfo
{
    private $dbFactory;
    private $System;

    public function __construct()
    {
        $this->dbFactory = new DBconnectionFactory();
        $this->System = new System;
    }

    public function getProjectInfoByRef($referenceNo)
    {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT DISTINCT ON (flw.id)
            flw.id,
            flw.system_id,
            flw.reference_no,
            flw.project_title,
            flw.utility_provider,
            flw.application_length,
            flw.application_date,
            flw.length_code,
            flw.appl_check_id,
            flw.type_application,
            flw.application_code,
            flw.project_region,
            flw.site_start,
            flw.site_end,
            flw.link_id,
            flw.user_created,
            flw.contact_id,
            flw.created_at,
            flw.submission_code,
            flw.charges_id,
            flw.project_costs,
            flw.project_date,
            flw.tags,
            flw.submitted_at,
            flw.payment_method,
            flw.utility_type,
            flw.districts,
            flw.state,
            flw.old_system,
            flw.old_system_id,
            flw.cancellation_notes,
            upi.state_code,
            upi.state_name,
            upi.district_code,
            upi.district_name
        FROM
            public.flw_appl_entries flw
        LEFT JOIN
            public.sys_upi upi ON flw.state = upi.state_code
            AND upi.district_code = ANY(flw.districts)
        WHERE 
            flw.reference_no = :referenceNo
        ORDER BY 
            flw.id, upi.id;";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':referenceNo', $referenceNo);
        $stmt->execute();
        $result = $stmt->fetchObject(); // Fetch result as stdClass object

        if ($result) {
            // Query returned at least one row
            // Optional: Typecast the result to object explicitly
            $result = (object) $result;
        } else {
            // Query did not return any rows
            $result = null;
        }

        return $result;
    }

    public function updateEntry($systemId, $column, $value)
    {
        $conn = $this->dbFactory->createConnection();

        $query = "UPDATE public.flw_appl_entries
            SET $column = :value
            WHERE system_id = :systemId;";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }

    public function getProjectInfoAll()
    {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT DISTINCT ON (flw.id)
            flw.id,
            flw.system_id,
            flw.reference_no,
            flw.project_title,
            flw.utility_provider,
            flw.application_length,
            flw.application_date,
            flw.length_code,
            flw.appl_check_id,
            flw.type_application,
            flw.application_code,
            flw.project_region,
            flw.site_start,
            flw.site_end,
            flw.link_id,
            flw.user_created,
            flw.contact_id,
            flw.created_at,
            flw.submission_code,
            flw.charges_id,
            flw.project_costs,
            flw.project_date,
            flw.tags,
            flw.submitted_at,
            flw.payment_method,
            flw.utility_type,
            flw.districts,
            flw.state,
            flw.old_system,
            flw.old_system_id,
            flw.cancellation_notes,
            upi.state_code,
            upi.state_name,
            upi.district_code,
            upi.district_name
        FROM
            public.flw_appl_entries flw
        LEFT JOIN
            public.sys_upi upi ON flw.state = upi.state_code
            AND upi.district_code = ANY(flw.districts)
        ORDER BY 
            flw.id, upi.id;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ); // Fetch result as stdClass object

        if ($result) {
            // Query returned at least one row
        } else {
            // Query did not return any rows
            $result = null;
        }

        return $result;
    }

    public function getProjectInfoAllWithAuth()
    {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT flw_entry.id,
    flw_entry.system_id,
    flw_entry.reference_no,
    flw_entry.project_title,
    flw_entry.utility_provider,
    flw_entry.application_length,
    flw_entry.application_date,
    flw_entry.length_code,
    flw_entry.appl_check_id,
    flw_entry.type_application,
    flw_entry.application_code,
    flw_entry.project_region,
    flw_entry.site_start,
    flw_entry.site_end,
    flw_entry.link_id,
    flw_entry.user_created,
    flw_entry.contact_id,
    flw_entry.created_at,
    flw_entry.submission_code,
    flw_entry.charges_id,
    flw_entry.project_costs,
    flw_entry.project_date,
    flw_entry.tags,
    flw_entry.submitted_at,
    flw_entry.payment_method,
    flw_entry.utility_type,
    flw_entry.districts,
    flw_entry.state,
    flw_entry.old_system,
    flw_entry.old_system_id,
    flw_entry.cancellation_notes,
    flw_entry.state_code,
    flw_entry.state_name,
    flw_entry.district_code,
    flw_entry.district_name,
    array_agg(json_build_object('id', flw_authority.authority_id, 'system_id', flw_authority.system_id, 'name', flw_authority.name, 'sort_name', flw_authority.sort_name, 'logo', flw_authority.logo, 'wl_date', flw_authority.wl_date, 'work_permit_id', flw_authority.work_permit_id, 'work_finish_id', flw_authority.work_finish_id, 'deposit_returns_id', flw_authority.deposit_returns_id)) AS authorities
   FROM ( SELECT DISTINCT ON (flw.id) flw.id,
            flw.system_id,
            flw.reference_no,
            flw.project_title,
            flw.utility_provider,
            flw.application_length,
            flw.application_date,
            flw.length_code,
            flw.appl_check_id,
            flw.type_application,
            flw.application_code,
            flw.project_region,
            flw.site_start,
            flw.site_end,
            flw.link_id,
            flw.user_created,
            flw.contact_id,
            flw.created_at,
            flw.submission_code,
            flw.charges_id,
            flw.project_costs,
            flw.project_date,
            flw.tags,
            flw.submitted_at,
            flw.payment_method,
            flw.utility_type,
            flw.districts,
            flw.state,
            flw.old_system,
            flw.old_system_id,
            flw.cancellation_notes,
            upi.state_code,
            upi.state_name,
            upi.district_code,
            upi.district_name
           FROM flw_appl_entries flw
             LEFT JOIN sys_upi upi ON flw.state::text = upi.state_code::text AND (upi.district_code::text = ANY (flw.districts::text[]))
          ORDER BY flw.id, upi.id) flw_entry
     LEFT JOIN ( SELECT ctrl.id,
            ctrl.system_id,
            ctrl.road_involved,
            ctrl.work_notice_id,
            ctrl.work_finish_id,
            ctrl.work_defects_id,
            ctrl.created_at,
            ctrl.updated_at,
            ctrl.flw_appl_report_id,
            ctrl.authority_id,
            ctrl.authority_status,
            ctrl.deposit_returns_id,
            ctrl.wayleave_id,
            ctrl.work_permit_id,
            ctrl.old_system,
            ctrl.old_system_id,
            list.name,
            list.sort_name,
            list.logo,
            wl.dt_appv_ltr AS wl_date
           FROM ctrl_authorities ctrl
             LEFT JOIN ls_authorities list ON list.id = ctrl.authority_id
             LEFT JOIN flw_wayleave wl ON wl.id = ctrl.wayleave_id[1]
          ORDER BY ctrl.id) flw_authority ON flw_entry.system_id::text = flw_authority.system_id::text
  GROUP BY flw_entry.id, flw_entry.system_id, flw_entry.reference_no, flw_entry.project_title, flw_entry.utility_provider, flw_entry.application_length, flw_entry.application_date, flw_entry.length_code, flw_entry.appl_check_id, flw_entry.type_application, flw_entry.application_code, flw_entry.project_region, flw_entry.site_start, flw_entry.site_end, flw_entry.link_id, flw_entry.user_created, flw_entry.contact_id, flw_entry.created_at, flw_entry.submission_code, flw_entry.charges_id, flw_entry.project_costs, flw_entry.project_date, flw_entry.tags, flw_entry.submitted_at, flw_entry.payment_method, flw_entry.utility_type, flw_entry.districts, flw_entry.state, flw_entry.old_system, flw_entry.old_system_id, flw_entry.cancellation_notes, flw_entry.state_code, flw_entry.state_name, flw_entry.district_code, flw_entry.district_name;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ); // Fetch result as stdClass object

        if ($result) {
            // Query returned at least one row
        } else {
            // Query did not return any rows
            $result = null;
        }

        return $result;
    }

    public function getAuthorityProjectInfo($systemId)
    {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT ctrl.*,
                        list.name,
                        list.sort_name,
                        list.logo,
                        wl.dt_appv_ltr AS wl_date
                    FROM public.ctrl_authorities ctrl 
                    LEFT JOIN public.ls_authorities list 
                        ON list.id = ctrl.authority_id 
                    LEFT JOIN public.flw_wayleave wl 
                        ON wl.id = ctrl.wayleave_id[1]  -- Join on the first element of the array
                    WHERE ctrl.system_id = :systemId 
                    ORDER BY ctrl.id ASC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ); // Fetch all rows as objects

        return $results;
    }
}