var prints = (function () {

  $(document).ready(function () {
    const a4Height = 842; // A4 height in pixels (adjust as needed)

    // $("section").each(function () {
    //   let contentHeight = 0;
    //   console.log({ section: $(this) });
    //   let contentEl = $(this).children();
    //   contentEl.each(function () {
    //     console.log({ child: $(this) });
    //     contentHeight += $(this).outerHeight();
    //     console.log(contentHeight);
    //     if (contentHeight >= a4Height) {
    //       $(this).before('</section><section class="sheet padding-10mm">');
    //       contentHeight = 0;
    //     }
    //   });
    // });

    $("section").each(function () {
      // const a4Height = 1000; // Adjust this value to the desired threshold
      let contentHeight = 0;
      let newSection = null;
      let contentToMove = $();
      let lastChildDataSectionTitle = null;

      $(this)
        .children()
        .each(function () {
          const childHeight = $(this).outerHeight();
          const lastChildDataSectionTitle = $(this).data("section-title");
          const lastChildInSection = $(this).children(":last");

          if (
            contentHeight + childHeight >= a4Height ||
            (lastChildInSection && lastChildDataSectionTitle)
          ) {
            // console.log('lastchild :' + $(lastChildDataSectionTitle).children(":last"));
            if (newSection) {
              // Append the accumulated content to the new section
              newSection.append(contentToMove);
            }
            // Create a new section before the current child
            newSection = $('<section class="sheet padding-10mm"></section>');
            $(this).parent().parent().find("section:last").after(newSection);

            // Reset the content height and content to move
            contentHeight = 0;
            contentToMove = $();
          }

          // Accumulate content height and content to move
          contentHeight += childHeight;
          contentToMove = contentToMove.add(this);
          console.log(contentToMove);
        });

      // Append any remaining content to the last section
      if (newSection) {
        newSection.append(contentToMove);
      }
    });
  });

  return {
    init: function () {},
  };
})();

KTUtil.onDOMContentLoaded(function () {
  prints.init();
});
