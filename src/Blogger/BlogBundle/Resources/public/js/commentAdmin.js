function getComments(blog) {
    $.ajax({
        type: 'GET',
        url: blog + '/getComments',
        success: function (comments) {
            appendComments(comments);
        }
    });
}

function appendComments(comments) {
    $("select[id$='parentId']").find('option').remove();

    for (var i = 0; i < comments.comments.length; i++) {
        var id = comments.comments[i].id;
        var comm = comments.comments[i].comment;
        $("select[id$='parentId']").append('<option value=id>comm</option>')
    }
}

$(document).ready(function () {
    $("select[id$='blog']").click(function () {
        getComments($(this).val());
    });
});