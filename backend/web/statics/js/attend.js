$(function(){
    var th=$('#attendBox th');
    var length=th.length;
    var target=$('#endrow td').eq(3);
    var left=th.eq(3).offset().left;
    var top=th.eq(3).offset().top-14;
    $('#triangle').css('left',left);
    $('#triangle').css('top',top);
    th.eq(3).attr('class','danger');
    $('#alert').text(target.text());
    th.click(function (e) {
        if($(this).index()==0||$(this).index()==1||$(this).index()==2||$(this).index()==length-1) return;
        var i;
        var index=$(this).index();
        for(i=3;i<length-1;i++){
            if(th.eq(i).attr('class')=='danger'){
                th.eq(i).removeAttr('class');
                break;
            }
        }
        left=$(this).offset().left;
        $('#triangle').css('left',left);
        $(this).attr('class','danger');
        $('#alert').text($('#endrow td').eq(index).text());

    });
})