jQuery(document).ready(function(o){function t(){var e=o(window).height()/2;o(".wplng-switcher").each(function(t){var n,s;o(this).hasClass("style-dropdown")&&(o(this).offset().top-o(window).scrollTop()<e?o(this).hasClass("open-bottom")||(o(this).addClass("open-bottom"),o(this).removeClass("open-top"),n=o(".wplng-languages",this).prop("outerHTML"),s=o(".wplng-language-current",this).prop("outerHTML"),o(".switcher-content",this).html(s+n)):o(this).hasClass("open-top")||(o(this).addClass("open-top"),o(this).removeClass("open-bottom"),n=o(".wplng-languages",this).prop("outerHTML"),s=o(".wplng-language-current",this).prop("outerHTML"),o(".switcher-content",this).html(n+s)))})}function n(){var t=parseInt(o("#wplng-in-progress-percent").html());t<100&&(t++,o("#wplng-in-progress-percent").html(t),o("#wplng-progress-bar-value").attr("style","width: "+t.toString()+"%"))}o("a[data-wplng-flag]").each(function(){var t=(t="<img ")+('src="'+o(this).attr("data-wplng-flag")+'" ')+'class="wplng-menu-flag"> ';o(this).html(t+o(this).html())}),o(window).scroll(function(){t()}),o("#wplng_style").on("input",function(){t()}),t(),o("#wplng-in-progress-iframe").on("load",function(){var t=o("#wplng-in-progress-container").attr("wplng-reload");window.location.href=t}),n(),o("#wplng-in-progress-percent").length&&setInterval(n,2e3),o("#wpadminbar").length&&o("#wplng-in-progress-container").length&&o("#wpadminbar").hide()});//# sourceMappingURL=script.js.map