$(document).ready(function () {
  $(".quality-tag").each(function () {
    var num = $(this).text();
    
    //Colour
    if (num > 0) {
      $(this).css("background", "rgb(" + Math.round(183 - num / 10) + "," + Math.round(28 + num / 4) + "," + Math.round(28 + num / 10) + ")");
    }
    else {
      $(this).css("background", "rgb(128, 128, 128)");
    }
    
    //Suffixes (p, HD, 2k, 4k)
    if (num > 0) {
      $(this).html("<span>" + num + "p</span>")
      if (num >= 720) {
        $(this).html("<span>" + num + "p</span><sup> HD<sup>")
      }
      if (num >= 1440 && num < 2160) {
        $(this).html("<span>" + num + "p</span><sup> 2K<sup>")
      }
      if (num >= 2160) {
        $(this).html("<span>" + num + "p</span><sup> 4K<sup>")
      }
    }
    else {
      $(this).html("<span>Unknown</span>")
    }
  });
  
  //Stream type (VLC, ACE, HTTP, etc)
  $(".stream-type-tag").each(function () {
    var type = $(this).text();
    var colour;
    if (type.toUpperCase() == "VLC") {
      colour = "#F57C00"
    }
    else if (type.toUpperCase() == "ACE" || type.toUpperCase() == "ACESTREAM") {
      colour = "#7B1FA2"
    }
    else if (type.toUpperCase() == "HTTP") {
      colour = "#388E3C"
    }
    else if (type.toUpperCase() == "SOP" || type.toUpperCase() == "SOPCAST") {
      colour = "#0277BD"
    }
    else if (type.toUpperCase() == "SD") {
      colour = "#976c3c"
    }
    else if (type.toUpperCase() == "HD") {
      colour = "#6fd064"
    }
    else {
      colour = "#00796B"
    }
    $(this).css("background", colour)
  });
});