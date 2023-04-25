$(document).ready(function() {
    var divElement = document.querySelectorAll(".image-as-lightbox");
    var imageElement = document.querySelectorAll(".image-as-lightbox + p > img");

    for(var i = 0; i < imageElement.length; i++) {
        var imgSrc = imageElement[i].src;
        divElement[i].innerHTML = `
            <a href="#screen-${i}">
              <img class="lightbox-thumbnail" src="${imgSrc}">
            </a>
            
            <a href="#_" class="lightbox" id="screen-${i}">
               <img src="${imgSrc}" />
            </a> 
        `;

        imageElement[i].remove();
    }
});