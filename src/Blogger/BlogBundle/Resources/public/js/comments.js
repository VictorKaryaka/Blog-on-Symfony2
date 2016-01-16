function getApiKey() {
    return 'OGEyOTMyNGVlMjQyMWQ2MDQ3ODdmZjNiNjA4OTUyMWFkMzQ1ODg2Nw';
}

function getBlog() {
    return 27;
}

function getComments() {
    $.ajax({
        type: 'GET',
        url: 'http://blogger.app/app_dev.php/api/comments/' + getBlog(),
        headers: {'apikey': getApiKey},
        success: function (data) {
            showComments(data);
        }
    });
}

function addComment(comment) {
    $.ajax({
        type: 'POST',
        url: 'http://blogger.app/app_dev.php/api/comments/' + getBlog(),
        headers: {'apikey': getApiKey()},
        data: JSON.stringify(comment),
        success: function (data) {
            showComments(data);
        }
    });
}

function showComments(data) {
    $('.comment').remove();
    $.each(data.comments, function (key, value) {
        console.log(value.user + ": " + value.comment);
        $('.previous-comments').append(
            '<article class="comment">' +
            '<header><p id="comment-header"><span class="highlight">' + value.user + '</span> ' +
            'оставил комментарий <time datetime = ' + new Date() + '>' + value.created + '' +
            '</time></p></header><p id="comment">' + value.comment + '</p>' +
            '<input id="but-comment" type="button" value="Comment">' +
            '</article>'
        );
    });
}

$(document).ready(function () {

    $("#add-comment").click(function () {
        var comment = $("#commentType_comment").val();
        if (comment != '') {
            addComment({'comment': comment});
            $("#commentType_comment").val('');
        }
    });

    getComments();

    $(".but-comment").click(function(){
        $("#hint").animate({
            top: $(this).offset().top + 23,
            left: $(this).offset().left - 540
        }, 400).fadeIn(800);
    });

    $(".close_hint").click(function(){ $("#hint").fadeOut(1200); });
});