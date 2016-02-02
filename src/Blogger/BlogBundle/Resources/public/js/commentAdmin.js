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

    if (comments.comments.length > 0) {
        $("div[id$='parentId']").show();
        $("select[id$='parentId']").append('<option value=""></option>');

        for (var i = 0; i < comments.comments.length; i++) {
            $("select[id$='parentId']").append('<option value=' + comments.comments[i].id + '>' +
                comments.comments[i].comment + '</option>');
        }
    } else {
        $("div[id$='parentId']").hide();
    }
}

$(document).ready(function () {
    if ($("select[id$='parentId']")[0].length == 1) {
        $("div[id$='parentId']").hide();
    }

    $("select[id$='blog']").click(function () {
        getComments($(this).val());
    });
});