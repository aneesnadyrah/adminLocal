"use strict";
var KTSurveyModal = (function () {
  var t, e, n, o, r, i, a;
  return {
    init: function () {
      (t = document.querySelector("#kt_survey_assign_modal")),
        (a = new bootstrap.Modal(t)),
        (i = document.querySelector("#kt_survey_assign_form")),
        (e = i.querySelector("#kt_survey_assign_submit")),
        (n = i.querySelector("#kt_survey_assign_cancel")),
        (o = t.querySelector("#kt_survey_assign_close")),
        (r = FormValidation.formValidation(i, {
          fields: {
            date: {
              validators: { notEmpty: { message: "Julat tarikh diperlukan" } },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
              rowSelector: ".fv-row",
              eleInvalidClass: "",
              eleValidClass: "",
            }),
          },
        })),
        e.addEventListener("click", function (t) {
          t.preventDefault(),
            r &&
              r.validate().then(function (t) {
                console.log("validated!"),
                  "Valid" == t
                    ? (e.setAttribute("data-kt-indicator", "on"),
                      (e.disabled = !0),
                      setTimeout(function () {
                        e.removeAttribute("data-kt-indicator"),
                          Swal.fire({
                            text: "Kumpulan telah berjaya dipilih!",
                            icon: "success",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok, maklum!",
                            customClass: { confirmButton: "btn btn-primary" },
                          }).then(function (t) {
                            t.isConfirmed && (a.hide(), (e.disabled = !1));
                          });
                      }, 2e3))
                    : Swal.fire({
                        text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, maklum!",
                        customClass: { confirmButton: "btn btn-primary" },
                      });
              });
        }),
        n.addEventListener("click", function (t) {
          t.preventDefault(),
            Swal.fire({
              text: "Adakah anda pasti mahu membatalkannya?",
              icon: "warning",
              showCancelButton: !0,
              buttonsStyling: !1,
              confirmButtonText: "Ya, batalkan!",
              cancelButtonText: "Tidak, kembali",
              customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-active-light",
              },
            }).then(function (t) {
              t.value
                ? (i.reset(), a.hide())
                : "cancel" === t.dismiss &&
                  Swal.fire({
                    text: "Borang anda belum dibatalkan!.",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "Ok, maklum!",
                    customClass: { confirmButton: "btn btn-primary" },
                  });
            });
        }),
        o.addEventListener("click", function (t) {
          t.preventDefault(),
            Swal.fire({
              text: "Adakah anda pasti mahu membatalkannya?",
              icon: "warning",
              showCancelButton: !0,
              buttonsStyling: !1,
              confirmButtonText: "Ya, batalkan!",
              cancelButtonText: "Tidak, kembali",
              customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-active-light",
              },
            }).then(function (t) {
              t.value
                ? (i.reset(), a.hide())
                : "cancel" === t.dismiss &&
                  Swal.fire({
                    text: "Borang anda belum dibatalkan!.",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "Ok, maklum!",
                    customClass: { confirmButton: "btn btn-primary" },
                  });
            });
        }),
        (function () {
          const t = i.querySelector("[name=date]");
          $(t).flatpickr({
            altInput: !0,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            mode: "range",
          });
        })();
    },
  };
})();
KTUtil.onDOMContentLoaded(function () {
  KTSurveyModal.init();
});
