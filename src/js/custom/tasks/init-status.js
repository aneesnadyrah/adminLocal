function renderAction(tenant, key, role) {
  let render;
  let tenantActions = {
    UCIDOS: {
      // Actions for UCIDOS
      // Add more conditions for UCIDOS
      // Render for StatusID 42
      // "042": function () {
      //   render = `<div class="text-center">
      //     <a href="projects/wayleave/feedback/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
      //       <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
      //     </a>
      //   </div>`;
      // },
    },
    KITER: {
      // Actions for KITER
      // Follow the same structure as UCIDOS
    },
    KUDRAT: {
      // Actions for KUDR
      // Follow the same structure as UCIDOS
      "006": function () {
        render = `<div class="text-center">
        <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}"
          data-bs-toggle="modal" data-bs-target="#action-${key["StatusID"]}${key["ID"]}">
            <i class="fad fa-file-upload fs-2"></i>
        </button>
      </div>`;
      },
      // "042": function () {
      //   render = `<div class="text-center">
      //     <a href="projects/wayleave/feedback/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
      //       <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
      //     </a>
      //   </div>`;
      // },
    },
    KUK: {
      // Actions for KUK
      // Follow the same structure as UCIDOS
    },
  };

  if (tenantActions[tenant]) {
    const actions = tenantActions[tenant];
    const statusID = key["StatusID"];
    const mappingID = key["MappingID"];

    if (actions[statusID]) {
      //Specific Actions
      actions[statusID](); // Call the corresponding function for the specific status

      //General Actions
    } else if (role === 61 || role === 62 || role === 63) {
      if (
        mappingID === "000" ||
        mappingID === "001" ||
        mappingID === "002" ||
        mappingID === "003" ||
        mappingID === "004" ||
        mappingID === "005" ||
        mappingID === "008" ||
        mappingID === "009" ||
        mappingID === "010" ||
        mappingID === "011" ||
        mappingID === "012" ||
        mappingID === "013" ||
        mappingID === "014" ||
        mappingID === "015"
      ) {
        render = `<div class="text-center">
        <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}"
          data-bs-toggle="modal" data-bs-target="#action-${key["MappingID"]}${key["StatusID"]}${key["ID"]}">
            <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </button>
      </div>`;
      } else if (
        (statusID === "042" ||
          statusID === "051" ||
          statusID === "052" ||
          statusID === "053" ||
          statusID === "043" ||
          statusID === "046" ||
          statusID === "047" ||
          statusID === "048" ||
          statusID === "062") &&
        mappingID === "006"
      ) {
        // Render for StatusID 62 with MappingID 4
        render = `<div class="text-center">
        <a href="surveys/site/progress/${key["SysID"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </a>
      </div>`;
      } else if (
        (statusID === "042" ||
          statusID === "051" ||
          statusID === "052" ||
          statusID === "053" ||
          statusID === "043" ||
          statusID === "046" ||
          statusID === "047" ||
          statusID === "048" ||
          statusID === "062") &&
        mappingID === "007"
      ) {
        // Render for StatusID 62 with MappingID 4
        render = `<div class="text-center">
        <a href="surveys/site/report/${key["SysID"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </a>
      </div>`;
      }
    } else if (statusID === "001") {
      // Render for StatusID 1 with non-null ProviderID
      render = `<div class="text-center">
        <a href="projects/record/check/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </a>
      </div>`;
    } else if (statusID === "013" || statusID === "014") {
      // Render for StatusID 13
      render = `<div class="text-center">
          <a href="reports/site/task/${key["SysID"]}?ref=${key["ReportNo"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
            <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
          </a>
        </div>`;
    } else if (statusID === "015") {
      // Render for StatusID 13
      render = `<div class="text-center">
          <a href="reports/site/review/${key["SysID"]}?ref=${key["ReportNo"]}&hl=true" class="btn btn-icon btn-light-${key["StatusColor"]}">
            <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
          </a>
        </div>`;
    } else if (statusID === "017") {
      // Render for StatusID 13
      render = `<div class="text-center">
          <a href="reports/site/amend/review/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
            <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
          </a>
        </div>`;
    } else if (
      (statusID === "029" ||
        statusID === "031" ||
        statusID === "032" ||
        statusID === "033" ||
        statusID === "034" ||
        statusID === "040") &&
      role === 33
    ) {
      // Render for StatusID 29|31 with RoleID 33
      render = `<div class="text-center">
        <button type="button" class="btn btn-icon btn-light-warning"
          data-bs-toggle="modal" data-bs-target="#action-${key["StatusID"]}${key["ID"]}">
            <i class="fad fa-map-location-dot fs-2"></i>
        </button>
      </div>`;
    }  else if ( statusID == "040" ||statusID == "122" || statusID == "041" || statusID == "045" || statusID == "042" || statusID == "083" || statusID == "821" || statusID == "871" || statusID == "127" || statusID == "123" || statusID == "153" || statusID == "872" || statusID == "088" || statusID == "134" || statusID == "133" || statusID == "142" || statusID == "143" || statusID == "046" || statusID == "082" || statusID == "084" || statusID == "822" || statusID == "093" || statusID == "112" || statusID == "124" || statusID == "129" || statusID == "152" || statusID == "154" || statusID == "087" || statusID == "089" || statusID == "891" || statusID == "132" || statusID == "141" || statusID == "144" || statusID == "145") {
      // Render for go to authority table
      render = `<div class="text-center">
          <a href="projects/record/authority?sid=${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
            <i class="fad fa-arrow-right fs-3" onclick="handleIconClick();"></i>
          </a>
        </div>`;

      //Render for upload general task
      //   render = `<div class="text-center">
      //   <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}"
      //     data-bs-toggle="modal" data-bs-target="#action-${key["StatusID"]}${key["ID"]}">
      //       <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
      //   </button>
      // </div>`;

    } else if (statusID === "049") {
      render = `<div class="text-center">
      <a href="projects/record/authority?sid=${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
        <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
      </a>
    </div>`;
    } else if (statusID === "045") {
      // Render for StatusID 45
      // render = `<div class="text-center">
      //   <a href="projects/wayleave/approval/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
      //     <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
      //   </a>
      // </div>`;
    } else if (statusID === "043") {
      // Render for StatusID 43
      render = `<div class="text-center">
        <a href="projects/wayleave/wyFbApproval/${key["SysID"]}/2" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
        </a>
      </div>`;
    } else if (statusID === "047") {
      render = `<div class="text-center">
        <a href="projects/wayleave/wyFbAmend/${key["SysID"]}/4" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
        </a>
      </div>`;
    }
    // else if (statusID === "051") {
    //   // Render for StatusID 45
    //   render = `<div class="text-center">
    //     <a href="projects/wayleave/feedback/${key["SysID"]}" class="btn btn-icon btn-light-${key["StatusColor"]}">
    //       <i class="fad fa-${key["StatusIcon"]} fs-2" onclick="handleIconClick();"></i>
    //     </a>
    //   </div>`;
    // }
    else if (statusID === "081") {
      // Render for StatusID 82
      render = `<div class="text-center">
        <a href="projects/permit/checklist/${key["SysID"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </a>
      </div>`;
    } else {
      // Default render
      render = `<div class="text-center">
        <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}"
          data-bs-toggle="modal" data-bs-target="#action-${key["StatusID"]}${key["ID"]}">
            <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
        </button>
      </div>`;
    }
  }

  return render;
}
