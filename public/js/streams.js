/**
 * Created by iMezied on 18-Mar-17.
 */

$(function ($) {
  var copyStream = new Clipboard('.btn-copy');
  copyStream.on('success', function (e) {
    swal('Copied', e.text, "success");
  });
  
  $("#commentsModal").on("show.bs.modal", function (e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
  
  $('#stream_selector_old').on('change', function (e) {
    if ($(this).val() != '') {
      $('#quality_selector').prop('disabled', false);
      $('#language_selector').prop('disabled', false);
      var rex = new RegExp($(this).val());
      console.log(rex);
      if (rex == "/all/") {
        $('.clickable-row').show();
        clearFilter();
        $('#quality_selector').prop('disabled', true);
        $('#language_selector').prop('disabled', true);
      } else {
        $('.clickable-row').hide();
        $('.clickable-row').filter(function () {
          return rex.test($(this).text());
        }).show();
        
        var qualities = [];
        var languages = [];
        $('.clickable-row').each(function () {
          if ($(this).css('display') != 'none') {
            qualities.push($(this).find(".qualityValue").html());
            languages.push($(this).find(".languageValue").html());
          }
        });
        qualities.unshift('All');
        languages.unshift('All');
        var qualityOptions = jQuery.unique(qualities);
        var languageOpions = jQuery.unique(languages);
        $('#quality_selector').empty();
        $('#language_selector').empty();
        $.each(qualityOptions, function (i, p) {
          $('#quality_selector').append($('<option></option>').val(p).html(p));
        });
        $.each(languageOpions, function (i, p) {
          $('#language_selector').append($('<option></option>').val(p).html(p));
        });
      }
    } else {
      $('#quality_selector').prop('disabled', true);
      $('#language_selector').prop('disabled', true);
    }
    
  });
  
  
});


function filterText(el) {
  console.log(el);
  console.log($(el).val());
  
  var rex = new RegExp($(el).val());
  console.log(rex);
  if (rex == "/all/") {
    $('.clickable-row').show();
    clearFilter()
  } else {
    $('.clickable-row').hide();
    $('.clickable-row').filter(function () {
      return rex.test($(this).text());
    }).show();
  }
}

function clearFilter() {
  console.log('filter ....');
  $('.clickable-row').show();
}