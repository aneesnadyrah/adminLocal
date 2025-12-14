var printPKIL = (function () {

  $(document).ready(function () {
    const A4Height = 842; // in pixels

    const $mainSection = $('.main-section');
      // console.log(secondaryHeader);
    $mainSection.each(function () {
      let $currentSection = $(this);
      let currentSectionHeight = 0;
      let lltChild = null;
      let lltChildGap = 0;

      $(this).children().each(function () {
        lltChildGap++;
        const $child = $(this);
          const childHeight = $child.outerHeight();
          console.log($child);
          console.log(childHeight);

        // check if current child is section header should be follow each page setup
        if ($child.data('section-title') === 'LLT') {
          lltChild = $child;
          lltChildGap = 0;
        }

        if (currentSectionHeight + childHeight > A4Height && lltChild != $child) {
          const $newSection = $('<section class="sheet padding-10mm"></section>');
            $currentSection.after($newSection);

          // Append the secondaryHeader content to the new section
          $newSection.append(secondaryHeader);

          $currentSection = $newSection;
          currentSectionHeight = 0;
        }

        if (lltChild != $child) {
          if (lltChildGap == 1) {
            $currentSection.append(lltChild);
          }
        $currentSection.append($child);
        currentSectionHeight += childHeight;
        }
      });
    })

  });

$(document).ready(function () {
  const A4Height = 842;

  const $secondarySection = $('.secondary-section');
  // console.log(secondaryHeader);
$secondarySection.each(function () {
  let $currentSection2 = $(this);
  let currentSectionHeight2 = 0;
  let lltChild2 = null;
  let lltChildGap2 = 0;

  $(this).children().each(function () {
    lltChildGap2++;
    const $child2 = $(this);
      const childHeight2 = $child2.outerHeight();
      // console.log($child2);
      // console.log(childHeight2);

    // check if current child is section header should be follow each page setup
    if ($child2.data('section-title') === 'LLT') {
      lltChild = $child2;
      lltChildGap = 0;
    }

    if (currentSectionHeight2 + childHeight2 > A4Height && lltChild2 != $child2) {
      const $newSection2 = $('<section class="sheet padding-10mm"></section>');
        $currentSection2.after($newSection2);

      // Append the secondaryHeader content to the new section
      // $newSection.append(secondaryHeader);

      $currentSection2 = $newSection2;
      currentSectionHeight2 = 0;
    }

    if (lltChild2 != $child2) {
      if (lltChildGap2 == 1) {
        $currentSection2.append(lltChild2);
      }
    $currentSection2.append($child2);
    currentSectionHeight2 += childHeight2;
    }
  });
})
})

  return {
    init: function () {

    },
  };
})();

KTUtil.onDOMContentLoaded(function () {
  printPKIL.init();
});
