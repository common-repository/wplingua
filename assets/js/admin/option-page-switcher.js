jQuery(document).ready(function(l){function e(e){l(".wplng-switcher").removeClass("insert-bottom-right").removeClass("insert-bottom-center").removeClass("insert-bottom-left").addClass("insert-"+e)}function s(e){l(".wplng-switcher").removeClass("theme-light-double-smooth").removeClass("theme-grey-double-smooth").removeClass("theme-dark-double-smooth").removeClass("theme-light-double-square").removeClass("theme-grey-double-square").removeClass("theme-dark-double-square").removeClass("theme-light-simple-smooth").removeClass("theme-grey-simple-smooth").removeClass("theme-dark-simple-smooth").removeClass("theme-light-simple-square").removeClass("theme-grey-simple-square").removeClass("theme-dark-simple-square").removeClass("theme-blurwhite-double-smooth").removeClass("theme-blurwhite-double-square").removeClass("theme-blurwhite-simple-smooth").removeClass("theme-blurwhite-simple-square").removeClass("theme-blurblack-double-smooth").removeClass("theme-blurblack-double-square").removeClass("theme-blurblack-simple-smooth").removeClass("theme-blurblack-simple-square").addClass("theme-"+e),l(".wplng-switcher").hasClass("theme-blurwhite-double-smooth")||l(".wplng-switcher").hasClass("theme-blurwhite-double-square")||l(".wplng-switcher").hasClass("theme-blurwhite-simple-smooth")||l(".wplng-switcher").hasClass("theme-blurwhite-simple-square")?l("#wplng-switcher-preview-container").attr("style","background-color: #1d2327;"):l("#wplng-switcher-preview-container").attr("style","")}function t(e){l(".wplng-switcher").removeClass("style-list").removeClass("style-block").removeClass("style-dropdown").addClass("style-"+e)}function a(e){"none"==e&&"none"==l("#wplng_flags_style").val()&&(l("#wplng_flags_style").val("rectangular"),o("rectangular")),l(".wplng-switcher").removeClass("title-name").removeClass("title-original").removeClass("title-id").removeClass("title-none").addClass("title-"+e)}function o(e){var s;l(".wplng-switcher").length&&("none"==e&&"none"==l("#wplng_name_format").val()&&(l("#wplng_name_format").val("name"),a("name")),l(".wplng-switcher").removeClass("flags-circle").removeClass("flags-rectangular").removeClass("flags-wave").removeClass("flags-none").addClass("flags-"+e),"none"!=e)&&(s=(s=(s=(s=l(".wplng-switcher").html()).replaceAll("/wplingua/assets/images/circle/","/wplingua/assets/images/"+e+"/")).replaceAll("/wplingua/assets/images/rectangular/","/wplingua/assets/images/"+e+"/")).replaceAll("/wplingua/assets/images/wave/","/wplingua/assets/images/"+e+"/"),l(".wplng-switcher").html(s))}l("#wplng_insert").on("input",function(){e(l(this).val())}),l("#wplng_theme").on("input",function(){s(l(this).val())}),l("#wplng_style").on("input",function(){t(l(this).val())}),l("#wplng_name_format").on("input",function(){a(l(this).val())}),l("#wplng_flags_style").on("input",function(){o(l(this).val())}),e(l("#wplng_insert").val()),s(l("#wplng_theme").val()),t(l("#wplng_style").val()),a(l("#wplng_name_format").val()),o(l("#wplng_flags_style").val());var r=wp.codeEditor.initialize(l("#wplng_custom_css"),cm_settings);l(document).on("keypress",".CodeMirror",function(){l("#wplingua-inline-css").html(r.codemirror.doc.getValue())}),l(document).on("mouseup",".CodeMirror",function(){l("#wplingua-inline-css").html(r.codemirror.doc.getValue())}),l(document).on("blur",".CodeMirror",function(){l("#wplingua-inline-css").html(r.codemirror.doc.getValue())})});//# sourceMappingURL=option-page-switcher.js.map
