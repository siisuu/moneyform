!function(){function e(){for(var e=this;-1===e.className.indexOf("nav-menu");)"li"===e.tagName.toLowerCase()&&(-1!==e.className.indexOf("focus")?e.className=e.className.replace(" focus",""):e.className+=" focus"),e=e.parentElement}var a,t,s,n,i;if((a=document.getElementById("site-navigation"))&&void 0!==(t=a.getElementsByTagName("button")[0]))if(void 0!==(s=a.getElementsByTagName("ul")[0])){s.setAttribute("aria-expanded","false"),-1===s.className.indexOf("nav-menu")&&(s.className+=" nav-menu"),t.onclick=function(){-1!==a.className.indexOf("toggled")?(a.className=a.className.replace(" toggled",""),t.setAttribute("aria-expanded","false"),s.setAttribute("aria-expanded","false")):(a.className+=" toggled",t.setAttribute("aria-expanded","true"),s.setAttribute("aria-expanded","true"))},n=s.getElementsByTagName("a");for(var r=0,l=(i=s.getElementsByTagName("ul")).length;r<l;r++)i[r].parentNode.setAttribute("aria-haspopup","true");for(r=0,l=n.length;r<l;r++)n[r].addEventListener("focus",e,!0),n[r].addEventListener("blur",e,!0);!function(e){var a,t=e.querySelectorAll(".menu-item-has-children > a, .page_item_has_children > a");if("ontouchstart"in window){a=function(e){var a=this.parentNode;if(a.classList.contains("focus"))a.classList.remove("focus");else{e.preventDefault();for(var t=0;t<a.parentNode.children.length;++t)a!==a.parentNode.children[t]&&a.parentNode.children[t].classList.remove("focus");a.classList.add("focus")}};for(var s=0;s<t.length;++s)t[s].addEventListener("touchstart",a,!1)}}(a)}else t.style.display="none"}();
