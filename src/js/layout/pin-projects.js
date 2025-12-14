var PinProject = (function () {
  // Private variables
  var pinSearch
  var modalPin;
  var pinnedElement;
  var resultsElement;
  var emptyElement;
  var searchObject;

  var projectView = `
  <div class="mh-375px scroll-y me-n7 pe-7">
      <div class="rounded d-flex flex-stack bg-active-lighten p-4" data-pin-id="{{ID}}">
          <div class="d-flex align-items-center">
              <input type="text" name="pin-system-id" value="{{systemID}}" hidden />
              <div class="symbol symbol-35px symbol-circle">
                  <img alt="Pic" src="{{providerID}}" />
              </div>
              <div class="ms-5">
                  <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">{{referenceNo}}</a>
                  <div class="fw-semibold text-muted">{{District}}</div>
              </div>
          </div>
          <div class="ms-2">
              <button type="submit" data-kt-action-submit="{{action}}" class="btn btn-icon flex-shrink-0">
                  <span class="indicator-label"><i class="fad fa-star fs-2 text-{{color}}"></i></span>
                  <span class="indicator-progress">
                      <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                  </span>
              </button>
          </div>
      </div>
      <div class="border-bottom border-gray-300 border-bottom-dashed"></div>
  </div>`;

  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: false,
    progressBar: false,
    positionClass: "toastr-bottom-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "2000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  var getPinProject = function (e) {

    pinSearch.addEventListener("keydown", (event) => {
      if (event.keyCode === 13) {
        // 13 is the key code for the "enter" key
        event.preventDefault();
      }
    });

    api.get('projects/pins/lists').then(response => {
      var projects = response.data;

      // loop through the projects and add each project to the sidebar
      for (let i = 0; i < projects.length; i++) {
        let project = projects[i];
        let projectLink = $("<a>", {
          href: `/projects/details/${project.systemID}`,
          class: "btn btn-icon btn-light-secondary",
          "data-bs-toggle": "tooltip",
          "data-bs-trigger": "hover",
          "data-bs-placement": "right",
          "data-bs-dismiss": "click",
          title: project.referenceNo,
        });
        fetch("https://extercord.asiadebut.tech/api/provider/" + project.providerID).then(response => response.json()).then(data => {
          projectLink.append($("<img>", {
            src: data.logo,
            class: "mw-40px",
            alt: "",
          }));
        });
        projectLink.append(projectImage);

        let projectDiv = $("<div>", {
          class: "app-navbar-item flex-center px-1 py-2",
        });
        projectDiv.append(projectLink);

        $("#pin-project").append(projectDiv);
      }
      $('[data-bs-toggle="tooltip"]').tooltip();

      // loop through the projects and add each project to the sidebar
      for (let i = 0; i < projects.length; i++) {
        let project = projects[i];
        fetch("https://extercord.asiadebut.tech/api/provider/" + project.providerID).then(response => response.json()).then(data => {
            let pinned_project = projectView
            .replace(/{{ID}}/g, project.ID)
            .replace(/{{systemID}}/g, project.systemID)
            .replace(/{{providerID}}/g, data.logo)
            .replace(/{{referenceNo}}/g, project.referenceNo)
            .replace(/{{District}}/g, project.District)
            .replace(/{{action}}/g, "unpin-project")
            .replace(/{{color}}/g, "warning");

            $("#pinned-selection").append(pinned_project);
        });
        
      }

    }).catch(error => {
      console.log(error);
    })
  };

  // Private functions
  var pinProcesss = function (search) {

    api.get(`search/projects/${searchObject.getQuery()}?limit=5`).then(response => {

      var data = response.data;
      // Clear the results container
      resultsElement.innerHTML = "";

      // Loop through the data
      if (data.length > 0) {
        // Show recently viewed
        pinnedElement.classList.add("d-none");
        // Hide results
        resultsElement.classList.remove("d-none");
        // Show empty message
        emptyElement.classList.add("d-none");

        data.forEach(function (item) {
          // Create a new element to display the data
          var searchData = document.createElement("div");

          fetch("https://extercord.asiadebut.tech/api/provider/" + project.providerID).then(response => response.json()).then(data => {
            searchData.innerHTML = projectView
            .replace(/{{ID}}/g, item.ID)
            .replace(/{{systemID}}/g, item.systemID)
            .replace(/{{providerID}}/g, data.logo)
            .replace(/{{referenceNo}}/g, item.referenceNo)
            .replace(/{{District}}/g, item.District)
            .replace(/{{action}}/g, "pin-project")
            .replace(/{{color}}/g, "dark");

            // Append the new element to the results container
            resultsElement.appendChild(searchData);
        });

          // Get the submit button from the new element
          searchData.querySelectorAll('[data-kt-action-submit="pin-project"]').forEach(function (button) {
            button.addEventListener("click", function (e) {
              e.preventDefault();
              button.setAttribute("data-kt-indicator", "on");
              button.disabled = true;
              var parent = button.closest("[data-pin-id]");
              var input = parent.querySelector('input[name="pin-system-id"]');
              var newPin = JSON.stringify({
                systemID: input.value,
              });

              api.post('projects/pins', newPin).then(response => {

                // Hide loading indication
                button.removeAttribute("data-kt-indicator");

                // Enable button
                button.disabled = false;

                if (response.data && response.data.length > 0) {
                  for (let i = 0; i < response.data.length; i++) {
                    let project = response.data[i];
                    let projectLink = $("<a>", {
                      href: "/projects/details.php?sid=" + project.systemID,
                      class: "btn btn-icon btn-light-secondary",
                      "data-bs-toggle": "tooltip",
                      "data-bs-trigger": "hover",
                      "data-bs-placement": "right",
                      "data-bs-dismiss": "click",
                      title: project.refNo,
                    });

                    fetch("https://extercord.asiadebut.tech/api/provider/" + project.providerID).then(response => response.json()).then(data => {
                      projectLink.append($("<img>", {
                        src: data.logo,
                        class: "mw-40px",
                        alt: "",
                      }));
                    });

                    let projectDiv = $("<div>", {
                      class: "app-navbar-item flex-center px-1 py-3",
                    });
                    projectDiv.append(projectLink);

                    $("#pin-project").append(projectDiv);
                  }

                  $('[data-bs-toggle="tooltip"]').tooltip();

                  for (let i = 0; i < response.data.length; i++) {
                    let project = response.data[i];

                    fetch("https://extercord.asiadebut.tech/api/provider/" + project.providerID).then(response => response.json()).then(data => {
                      let pinned_project = projectView
                      .replace(/{{ID}}/g, project.ID)
                      .replace(/{{systemID}}/g, project.systemID)
                      .replace(/{{providerID}}/g, data.logo)
                      .replace(/{{referenceNo}}/g, project.referenceNo)
                      .replace(/{{District}}/g, project.District)
                      .replace(/{{action}}/g, "unpin-project")
                      .replace(/{{color}}/g, "warning");

                      $("#pinned-selection").append(pinned_project);
                    });
                    
                  }

                  toastr.success("Berjaya! Projek telah disematkan. 🌟");

                  setTimeout(function () {
                    document.querySelector('input[name="pin-search"]').value = "";
                    pinnedElement.classList.remove("d-none");
                    resultsElement.classList.add("d-none");
                    emptyElement.classList.add("d-none");

                    var modalElement = document.getElementById("modal-pin-project");
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                  }, 1000);
                } else {
                  toastr.warning("Maaf, Tiada projek dijumpai. Sila cuba permohonan lain.");
                }

              }).catch(error => {

                console.log(error);
                // Hide loading indication
                button.removeAttribute("data-kt-indicator");
                // Enable button
                button.disabled = false;

                toastr.error(
                  "Maaf, Terdapat Ralat di dalam sistem. Sila cuba sekali lagi."
                );
              })
            })
          })
        });

      } else {
        // Hide results
        resultsElement.classList.add("d-none");
        // Show empty message
        emptyElement.classList.remove("d-none");
        pinnedElement.classList.add("d-none");
      }
    }).catch(error => {
      console.log(error);
      // Hide results
      resultsElement.classList.add("d-none");
      // Show empty message
      emptyElement.classList.remove("d-none");
    });
    search.complete();
  };



  var pinClear = function (search) {
    // Show recently viewed
    pinnedElement.classList.remove("d-none");
    // Hide results
    resultsElement.classList.add("d-none");
    // Hide empty message
    emptyElement.classList.add("d-none");
  };

  // Public methods
  return {
    init: function () {
      // Elements
      pinSearch = document.querySelector('input[name="pin-search"]');
      modalPin = document.querySelector("#modal-pin-project-handler");

      if (!modalPin || !pinSearch) {
        return;
      }

      wrapperElement = modalPin.querySelector(
        '[data-kt-search-element="wrapper"]'
      );
      pinnedElement = modalPin.querySelector(
        '[data-kt-search-element="pinned"]'
      );
      resultsElement = modalPin.querySelector(
        '[data-kt-search-element="results"]'
      );
      emptyElement = modalPin.querySelector('[data-kt-search-element="empty"]');

      // Initialize search handler
      searchObject = new KTSearch(modalPin);

      // Search handler
      searchObject.on("kt.search.process", pinProcesss);

      // Clear handler
      searchObject.on("kt.search.clear", pinClear);

      getPinProject();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  // PinProject.init();
  PinProject.init();
});