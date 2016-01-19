function postComment() {
    $.ajax({
        type: 'POST',
        url: $("#form-comment")[0].action,
        data: $("#form-comment").serialize(),
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
            postComment();
            $("#commentType_comment").val('');
            $('.not-comment').remove();
        }
    });

    //$(".but-comment").click(function(){
    //    $("#hint").animate({
    //        top: $(this).offset().top + 23,
    //        left: $(this).offset().left - 540
    //    }, 400).fadeIn(800);
    //});
    //
    //$(".close_hint").click(function(){ $("#hint").fadeOut(1200); });
});