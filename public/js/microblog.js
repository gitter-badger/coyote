$(function(){"use strict";function t(t,e){var n=$(t).clone().html();for(var i in e)n=n.replace("[["+i+"]]",e[i]);return n}function e(e){var n=function(){$(this).parent().remove()};e.submit(function(){}).delegate("#btn-upload","click",function(){$(".input-file",e).click()}).delegate(".input-file","change",function(){var o=this.files[0];if("image/png"!==o.type&&"image/jpg"!==o.type&&"image/gif"!==o.type&&"image/jpeg"!==o.type)$("#alert").modal("show"),$(".modal-body").text("Format pliku jest nieprawidłowy. Załącznik musi być zdjęciem JPG, PNG lub GIF");else{var a=new FormData(e[0]);$.ajax({url:uploadUrl,type:"POST",data:a,cache:!1,contentType:!1,processData:!1,beforeSend:function(){$(".thumbnails").append(t("#tmpl-thumbnail",{src:i,"class":"spinner",fa:"fa fa-spinner fa-spin fa-2x"}))},success:function(t){var e=$(".thumbnail:last");$(".spinner",e).remove(),$("img",e).attr("src",t.url),$("<div>",{"class":"btn-delete"}).html('<i class="fa fa-remove fa-2x"></i>').click(n).appendTo(e)},error:function(t){$("#alert").modal("show"),"undefined"!=typeof t.responseJSON&&$(".modal-body").text(t.responseJSON.photo[0]),$(".thumbnail:last").remove()}},"json")}})}var n,i="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAfQAAADIAQMAAAAk14GrAAAABlBMVEXd3d3///+uIkqAAAAAI0lEQVRoge3BMQEAAADCoPVPbQ0PoAAAAAAAAAAAAAAAAHg2MgAAAYXziG4AAAAASUVORK5CYII=",o={click:function(){var t=parseInt($(this).data("count")),e=$(this);e.addClass("loader").text("Proszę czekać..."),$.post(voteUrl+e.data("id"),function(n){t=parseInt(n.count),e.toggleClass("thumbs-on"),e.data("count",t)}).complete(function(){e.removeClass("loader"),e.text(t+" "+declination(t,["głos","głosy","głosów"]))}).fail(function(t){$("#alert").modal("show"),$(".modal-body").text(t.responseJSON.error)})},enter:function(){var t=parseInt($(this).data("count"));if(t>0){var e=$(this);"undefined"==typeof e.attr("title")&&(n=setTimeout(function(){$.get(voteUrl+e.data("id"),function(t){if(e.attr("title",t),t.length){var n=t.split("\n").length;e.attr("title",t.replace(/\n/g,"<br />")).data("count",n).text(n+" "+declination(n,["głos","głosy","głosów"])).tooltip({html:!0}).tooltip("show")}})},500))}$(this).off("mouseenter")},leave:function(){clearTimeout(n),$(".tooltip").remove()}};$("#microblog").on("click",".btn-reply",function(){$(this).parent().next(".microblog-comments").find("input").focus()}).on("click",".btn-watch",function(){var t=$(this);$.post(microblogUrl+"/Watch/"+parseInt(t.data("id")),function(){t.toggleClass("watch-on")}).fail(function(t){$("#alert").modal("show"),$(".modal-body").text(t.responseJSON.error)})}).on("click",".btn-thumbs, .btn-sm-thumbs",o.click).on("mouseenter",".btn-thumbs, .btn-sm-thumbs",o.enter).on("mouseleave",".btn-thumbs, .btn-sm-thumbs",o.leave),e($(".microblog-submit"))}),function(t){t.fn.prompt=function(e){return this.each(function(){var n=t(this),i=-1,o=0,a=t('<ul style="display: none;" class="auto-complete"></ul>'),s=function(){if(n[0].selectionStart||0==n[0].selectionStart)return n[0].selectionStart;if(document.selection){n.focus();var t=document.selection.createRange();return t.moveStart("character",-n.value.length),t.text.length}},r=function(t){for(var e=t,i=-1;e>t-50&&e>=0;){var o=n.val()[e];if(" "===o)break;if("@"===o&&(0===e||" "===n.val()[e-1]||"\n"===n.val()[e-1])){i=e+1;break}e--}return i},c=function(e){var n=t("li",a).length;n>0&&(e>=n?e=0:0>e&&(e=n-1),i=e,t("li",a).removeClass("hover"),t("li:eq("+i+")",a).addClass("hover"))};n.bind("keyup click",function(l){var u="",h=l.keyCode,f=s(),d=r(f);d>-1&&(u=n.val().substr(d,f-d));var m=function(){var e=t("li.hover span",a).text();if(e.length){(e.indexOf(" ")>-1||e.indexOf(".")>-1)&&(e="{"+e+"}"),n.val(n.val().substr(0,d)+e+n.val().substring(f)).trigger("change").focus();var i=d+e.length;if(n[0].setSelectionRange)n[0].setSelectionRange(i,i);else if(n[0].createTextRange){var o=n[0].createTextRange();o.collapse(!0),o.moveEnd("character",i),o.moveStart("character",i),o.select()}}a.html("").hide()};switch(h){case 27:a.html("").hide();break;case 40:c(i+1);break;case 38:c(i-1);break;case 13:m();break;default:u.length>=2?(clearTimeout(o),o=setTimeout(function(){t.get(e,{q:u},function(e){a.html(e).hide();var o=t("li",a).length;if(o>0){var s=n.offset();t("li",a).click(m).hover(function(){t("li",a).removeClass("hover"),t(this).addClass("hover")},function(){t(this).removeClass("hover")}),a.css({width:n.outerWidth(),top:n.outerHeight()+s.top+1,left:s.left}).show(),i=-1}})},200)):a.html("").hide()}}).keydown(function(t){var e=t.keyCode;return 40!==e&&38!==e&&13!==e&&27!==e||!a.is(":visible")?void 0:(t.preventDefault(),!1)}),a.appendTo(document.body),t(document).bind("click",function(e){var n=t(e.target);n.not(a)&&a.html("").hide()})})}}(jQuery),function(t){"use strict";t.fn.autogrow=function(){return this.each(function(){var e=t(this),n=e.height(),i=300,o=(e.css("lineHeight"),0),a=0,s=t("<div></div>").css({position:"absolute",top:-1e4,left:-1e4,width:t(this).width()-parseInt(e.css("paddingLeft"))-parseInt(e.css("paddingRight")),fontSize:e.css("fontSize"),fontFamily:e.css("fontFamily"),lineHeight:e.css("lineHeight"),resize:"none"}).appendTo(document.body),r=function(){var e=function(t,e){for(var n=0,i="";e>n;n++)i+=t;return i},o=this.value.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/&/g,"&amp;").replace(/\n$/,"<br/>&nbsp;").replace(/\n/g,"<br/>").replace(/ {2,}/g,function(t){return e("&nbsp;",t.length-1)+" "});s.html(o),t(this).css("height",Math.max(Math.min(s.height()+17,i),n))};e.change(r).keyup(r).keydown(r),r.apply(this),e.mousedown(function(){o=e.width(),a=e.height()}).mouseup(function(){(e.width()!=o||e.height()!=a)&&e.unbind("keyup",r).unbind("keydown",r).unbind("change",r),o=a=0})})}}(jQuery),function(t){"use strict";t.fn.fastSubmit=function(){return this.each(function(){t(this).keydown(function(e){if(13===e.keyCode){var n=t(this).closest("form");e.ctrlKey&&n.submit()}})})}}(jQuery);
//# sourceMappingURL=microblog.js.map