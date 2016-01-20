function postComment(comment, parentId) {
    $.ajax({
        type: 'POST',
        url: $("#form-comment")[0].action,
        //data: {'comment': comment.serialize(), 'parentId': parentId},
        data: {'comment': comment.serialize()},
        success: function (data) {
            appendComment(data);
        }
    });
}

function appendComment(data) {
    $('.previous-comments').append(
        '<article class="comment">' +
        '<header><p id="comment-header"><span class="highlight">' + data.user + '</span> ' +
        'оставил комментарий ' + data.created.date +
        '</p></header><p id="comment">' + data.comment + '</p>' +
        '<input class="but-comment" type="button" value="Comment">' +
        '</article>'
    );
}

$(document).ready(function () {

    $("#form-comment").submit(function () {
        event.preventDefault();
        var comment = $("#form-comment").text();

        if (comment != '') {
            postComment($(this));
            $("#commentType_comment").val('');
            $('.not-comment').remove();
        }
    });

    $(".but-comment").click(function () {
        $(this).parent().find('.last').css('display', 'block');
    });


    $('body').on('click', '.close_hint', function () {
        $(this).parents('.last').css('display', 'none');
    });

    $('body').on('click', '.send-comment', function () {
        var id = $(this).parents('article').attr('id');
        postComment($(this).parents('form'), Number(id.match(/\d+/)));
    });

});