function postComment(commentForm, parentId) {
    blockUI();
    var comment = commentForm.serializeArray()[0].value;
    $.ajax({
        type: 'POST',
        url: $("#form-comment")[0].action,
        data: {commentType: {'comment': comment, 'parentId': parentId}},
        success: function (data) {
            $.unblockUI();
            appendComment(data);
        },
        error: function (jqXHR) {
            if (403 == jqXHR.status) {
                var path = window.location.pathname;
                window.location.replace(path.substr(0, path.lastIndexOf('php') + 3) + '/login');
            }
        }
    }).always(function () {
        $.unblockUI();
    });
}

function editComment(commentForm, parentId) {
    blockUI();
    var comment = commentForm.serializeArray()[0].value;
    $.ajax({
        type: 'POST',
        url: 'comment/edit/' + parentId,
        data: {'comment': comment},
        success: function (data) {
            if (data.notice == 'success') {
                $.unblockUI();
                $('#comment-' + parentId).find('#comment').text(data.comment);
            }
        },
        error: function (jqXHR) {
            if (403 == jqXHR.status) {
                var path = window.location.pathname;
                window.location.replace(path.substr(0, path.lastIndexOf('php') + 3) + '/login');
            }
        }
    }).always(function () {
        $.unblockUI();
    });
}

function deleteComment(id) {
    blockUI();
    $.ajax({
        url: 'comment/delete/' + id,
        success: function (data) {
            if (data.notice == 'success') {
                $.unblockUI();
                $('#comment-' + id).find('#comment').text('This comment is deleted!');
                $('#comment-' + id).find('#group-button').remove();
            }
        },
        error: function (jqXHR) {
            if (403 == jqXHR.status) {
                var path = window.location.pathname;
                window.location.replace(path.substr(0, path.lastIndexOf('php') + 3) + '/login');
            }
        }
    }).always(function () {
        $.unblockUI();
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
            '</p></header><p id="comment">' + data.comment + '</p> <div id="group-button">' +
            '<input class="button-add" id="but-comment" type="button" value="Comment">' +
            '<input class="button-edit" id="but-edit" type="button" value="Edit">' +
            '<input class="button-delete" id="but-delete" type="button" value="Delete"></div>' +
            '</article>';
    };

    if (data.parentId != null) {
        margin = 30 + parseInt($('#comment-' + data.parentId).css('margin-left'));
        $('.previous-comments').find('#comment-' + data.parentId).after(appendForm(margin));
    } else {
        $('.previous-comments').append(appendForm(margin));
    }
}

function blockUI() {
    $.blockUI({
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}

$(document).ready(function () {
    var editMode = false;

    function appendCommentForm(form) {
        var editText = '';

        if (editMode) {
            editText = form.find('#comment').text();
        }

        var block = '<div class="last" align="center">' +
            '<form method="post" enctype="multipart/form-data" class="add_comment" id="hint">' +
            '<textarea cols="30" rows="4" class="commentType_comment" name="commentType[comment]"' +
            'required="required">' + editText + '</textarea>' +
            '<div>' +
            '<input class="send-comment" type="button" value="Отправить">' +
            '<input type="button" class="close_hint" value="Закрыть">' +
            '</div>' +
            '</form>' +
            '</div>';

        $(form).append(block);
    }

    $("#form-comment").submit(function () {
        event.preventDefault();

        if ($("#form-comment").text() != '') {
            postComment($(this));
            $("#commentType_comment").val('');
            $('.not-comment').remove();
        }
    });

    $('body').on('click', '#but-comment', function () {
        $('body').find('.last').remove();
        appendCommentForm($(this).parents('article'));
    });

    $('body').on('click', '.send-comment', function () {
        var id = $(this).parents('article').attr('id');
        var message = $(this).parents('article').find('.commentType_comment').val();

        if (message != '') {
            if (editMode) {
                editComment($(this).parents('form'), Number(id.match(/\d+/)));
                editMode = false;
            } else {
                postComment($(this).parents('form'), Number(id.match(/\d+/)));
            }
            $('body').find('.last').remove();
        } else {
            $(".last").notify('Заполните поле!', {position: "top center"}, 'error');
        }
    });

    $('body').on('click', '.close_hint', function () {
        editMode = false;
        $(this).parents('.last').remove();
    });

    $('body').on('click', '#but-edit', function () {
        editMode = (!editMode) ? true : false;
        $('body').find('.last').remove();
        appendCommentForm($(this).parents('article'));

        if (editMode == false) {
            $('.close_hint').parents('.last').remove();
        }
    });

    $('body').on('click', '#but-delete', function () {
        if (confirm('Do you want delete this comment?')) {
            var id = $(this).parents('article').attr('id');
            deleteComment(Number(id.match(/\d+/)));
        }
    });
});