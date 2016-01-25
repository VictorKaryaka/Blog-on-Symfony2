function postComment(commentForm, parentId) {
    var comment = commentForm.serializeArray()[0].value;
    $.ajax({
        type: 'POST',
        url: $("#form-comment")[0].action,
        data: {commentType: {'comment': comment, 'parentId': parentId}},
        success: function (data) {
            appendComment(data);
        }
    });
}

function appendComment(data) {
    var margin = 0;

    var appendForm = function (margin) {
        return '<article class="comment"' +
            'id="comment-' + data.id + '"' +
            'style="margin-left: ' + margin + 'px">' +
            '<header><p id="comment-header"><span class="highlight">' + data.user + '</span> ' +
            'оставил комментарий ' + data.created.date +
            '</p></header><p id="comment">' + data.comment + '</p>' +
            '<input class="but-comment" type="button" value="Comment">' +
            '</article>';
    };

    if (data.parentId != null) {
        margin = 30 + parseInt($('#comment-' + data.parentId).css('margin-left'));
        $('.previous-comments').find('#comment-' + data.parentId).after(appendForm(margin));
    } else {
        $('.previous-comments').append(appendForm(margin));
    }
}

function appendCommentForm(form) {
    var block = '<div class="last" align="center">' +
        '<form method="post" enctype="multipart/form-data" class="add_comment" id="hint">' +
        '<textarea cols="30" rows="4" class="commentType_comment" name="commentType[comment]" required="required">' +
        '</textarea>' +
        '<div>' +
        '<input class="send-comment" type="button" value="Отправить">' +
        '<input type="button" class="close_hint" value="Закрыть">' +
        '</div>' +
        '</form>' +
        '</div>';

    $(form).append(block);
}

$(document).ready(function () {

    $("#form-comment").submit(function () {
        event.preventDefault();

        if ($("#form-comment").text() != '') {
            postComment($(this));
            $("#commentType_comment").val('');
            $('.not-comment').remove();
        }
    });

    $('body').on('click', '.but-comment', function () {
        $('body').find('.last').remove();
        appendCommentForm($(this).parents('article'));
    });

    $('body').on('click', '.send-comment', function () {
        var id = $(this).parents('article').attr('id');
        var message = $(this).parents('article').find('.commentType_comment').val();

        if (message != '') {
            postComment($(this).parents('form'), Number(id.match(/\d+/)));
            $('body').find('.last').remove();
        } else {
            $(".last").notify('Заполните поле!', {position: "top center"}, 'error');
        }
    });

    $('body').on('click', '.close_hint', function () {
        $(this).parents('.last').remove();
        $().hide();
    });
});