$(document).ready(function () {
    $('#but-edit').click(function () {
        if ($('#update-blog').is(':hidden')) {
            $('#update-blog').show('slow');
        } else {
            $('#update-blog').hide('slow');
        }
    });
});
