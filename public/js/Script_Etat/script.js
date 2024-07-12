$(document).ready(function() {
    $('.accordion-header').click(function() {
        var accordionItem = $(this).parent();
        var accordionContent = accordionItem.find('.accordion-content');

        // Toggle active class on header
        $(this).toggleClass('active');

        // Toggle visibility of accordion content
        if (accordionContent.is(':visible')) {
            accordionContent.slideUp();
        } else {
            accordionContent.slideDown();
        }
    });

    $('#searchForm').submit(function(event)
    {
        event.preventDefault();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        if (startDate === '' || endDate === '') {
            toastr.warning('Veuillez remplir la date de début et la date de fin.','attention');
            return;
        }
        this.submit();
    });
});
