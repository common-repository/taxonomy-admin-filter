(function ($) {
	function stripDiacritics (text) {
    // Used 'uni range + named function' from http://jsperf.com/diacritics/18
    function match(a) {
			var DIACRITICS={"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S","Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z","Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n","ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ":"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α","Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ω":"ω","ς":"σ"}
      return DIACRITICS[a] || a;
    }

    return text.replace(/[^\u0000-\u007E]/g, match);
  }
  var options = taxonomy_admin_filter_options;
  $(options.selector).each(function () {
    var $this = $(this);
    $this.addClass('taxonomy-admin-filter');
    var $product_cat_tax = $this;
    var product_cat_tax_id = $product_cat_tax.attr('id');
    var taxonomy = product_cat_tax_id.substring(21);
    var tax_options = options.taxonomies[taxonomy];
    var lang = options.lang;
    if (!tax_options.select2 && tax_options.hideblank === 1) {
      var tax_is_empty = $this.is(':empty');
      if (tax_is_empty) {
        return;
      }
    }
    var search_input_id = product_cat_tax_id + '-searchinput';
    var $search_input = $('<input />').attr({
      id: search_input_id,
      type: 'search',
      class: 'taxonomy-admin-filter-label',
      placeholder: lang.placeholder
    });
    var search_style_id = product_cat_tax_id + '-searchstyle';
    var $search_style = $('<style />').attr({
      id: search_style_id,
      type: 'text/css'
    });
    $product_cat_tax.before($search_style);
    if (!tax_options.select2)
    {
      var search_check_all_id = product_cat_tax_id + '-checkall';
      var $search_check_all = $('<input />').attr({
        id: search_check_all_id,
        title: lang.select_all,
        type: 'checkbox'
      });
    }
    var search_uncheck_all_id = product_cat_tax_id + '-uncheckall';
    var $search_uncheck_all = $('<input />').attr({
      id: search_uncheck_all_id,
      title: lang.deselect_all,
      type: 'checkbox'
    });
    var search_check_count_id = product_cat_tax_id + '-checkcount';
    var $search_check_count = $('<input />').attr({
      id: search_check_count_id,
      title: lang.show_unallocated,
      type: 'checkbox'
    });
    var search_check_cnt_id = product_cat_tax_id + '-checkcnt';
    var $search_check_cnt = $('<input />').attr({
      id: search_check_cnt_id,
      title: lang.hide_empty,
      type: 'checkbox'
    });
    if (!tax_options.select2)
    {
      var search_check_select_id = product_cat_tax_id + '-checkselect';
      var $search_check_select = $('<input />').attr({
        id: search_check_select_id,
        title: lang.show_selected,
        type: 'checkbox'
      });
    }
    var $checkbox_container = $this;
    var $checkbox_ul = $this;

    $checkbox_ul.on('change', 'input[type=checkbox]', function (event) {
      var $this = $(this);
      var $parent_li = $this.closest('li');
      if (tax_options.select2) {
        if (!this.checked) {
          $parent_li.remove();
        }
      } else {
        $parent_li.attr('data-selected', this.checked ? '1' : '0');
      }
      if (tax_options.select2) {
        calculateTotal();
      }
      calculateSelected();
    });
    var last_clicked = -1;
    $checkbox_ul.on('click', 'input[type=checkbox]', function (event) {
      var current_index = $(this).closest('li').index();
      if (event.shiftKey) {
        var gt = Math.min(last_clicked, current_index) - 1;
        var lt = Math.max(last_clicked, current_index) + 1;
        $('li:lt(' + lt + '):gt(' + gt + ')>label:visible input[type=checkbox]' + (this.checked ? ':not(:checked)' : ':checked'), $checkbox_ul).prop('checked', this.checked).trigger('change');
      }
      last_clicked = current_index;
    });

    var checkbox_container_ul_id = $checkbox_container.attr('id');
    var results_count_id = product_cat_tax_id + '-results_count_id';
    var $results_count_div = $('<span />').attr({
      id: results_count_id,
      class: 'taxonomy-admin-filter-label'
    });
    var total_count_id = product_cat_tax_id + '-total_count_id';
    var $total_count_div = $('<span />').attr({
      id: total_count_id,
      class: 'taxonomy-admin-filter-label'
    });
    var selected_count_id = product_cat_tax_id + '-selected_count_id';
    var $selected_count_div = $('<span />').attr({
      id: selected_count_id,
      class: 'taxonomy-admin-filter-label'
    });
    var filter_id = product_cat_tax_id + '-filters';
    var $filter_container = $('<div />').attr({
      id: filter_id,
      class: 'taxonomy-admin-filters top-filters'
    });
    var bottom_filter_id = product_cat_tax_id + '-bottom-filters';
    var $bottom_filter_container = $('<div />').attr({
      id: bottom_filter_id,
      class: 'taxonomy-admin-filters bottom-filters'
    });

    var $div = $('<div />').attr({class: "taxonomy-admin-filter-totals"})
    $bottom_filter_container.append($div);
    $div.append($selected_count_div);
    $div.append($results_count_div);
    $div.append($total_count_div);

    var $label = $('<label />').attr({class: "taxonomy-admin-filter-label"});
    $checkbox_container.before($filter_container);
    $checkbox_container.after($bottom_filter_container);
    $filter_container.append($search_input);
    if (!tax_options.select2)
    {
      var $search_check_select_label = $label.clone().attr({title: $search_check_select.attr('title')}).text(lang.show_selected_label).prepend($search_check_select);
      $bottom_filter_container.append($search_check_select_label);
    }
    var $search_check_count_label = $label.clone().attr({title: $search_check_count.attr('title')}).text(lang.show_unallocated_label).prepend($search_check_count);
    $bottom_filter_container.append($search_check_count_label);
    var $search_check_cnt_label = $label.clone().attr({title: $search_check_cnt.attr('title')}).text(lang.hide_empty_label).prepend($search_check_cnt);
    $bottom_filter_container.append($search_check_cnt_label);
    if (!tax_options.select2)
    {
      var $search_check_all_label = $label.clone().attr({title: $search_check_all.attr('title')}).text(lang.select_all_label).prepend($search_check_all);
      $filter_container.append($search_check_all_label);
    }
    var $search_uncheck_all_label = $label.clone().attr({title: $search_uncheck_all.attr('title')}).text(lang.deselect_all_label).prepend($search_uncheck_all);
    $filter_container.append($search_uncheck_all_label);
    function add_selected_option(item) {
      var $checkbox = $('<input />').attr({
        id: 'in-' + taxonomy + '-' + item.id,
        type: 'checkbox',
        value: item.id,
        name: options.profile_name + '[]',
        checked: 'checked'
      });
      var $tax_counter = $('<span />').attr({
        class: 'category_post_type_count',
        title: lang.item_count_tax + ' (' + options.post_type + ')'
      }).text('(' + item.count_tax + ')');

      var $counter = $('<span />').attr({
        class: 'category_count',
        title: lang.item_count
      }).text('[' + (item.count - item.count_tax) + ']');

      var $label = $('<label />').attr({
        class: 'selectit'
      }).text(item.text).append($tax_counter).append($counter);
      var $checkbox_li = $('<li />').attr({
        id: taxonomy + '-' + item.id,
        'data-contentlc': stripDiacritics(item.text).toLowerCase(),
        'data-content': stripDiacritics(item.text),
        'data-cnt': item.count - item.count_tax,
        'data-count': item.count_tax,
        'data-selected': 1
      });
      $checkbox_ul.append($checkbox_li);
      $checkbox_li.append($label);
      $label.prepend($checkbox);
      return $checkbox;
    }
    if (tax_options.select2)
    {
      var $select2_select = $('<select />').attr({
        id: product_cat_tax_id + '-select2',
      });
      $filter_container.append($select2_select);
      $select2_select.select2({
        width: '100%',
        allowClear: true,
        minimumInputLength: 0,
        minimumResultsForSearch: 0,
        placeholder: lang.placeholder,
        escapeMarkup: function (markup) {
          return markup;
        },
        templateResult: function (item, li) {
          $(li).addClass('taxonomy-admin-select2');
          if (!item.id)
          {
            return item.text;
          }
          if ($('#in-' + taxonomy + '-' + item.id + ':checked').length)
          {
            $(li).addClass('taxonomy-admin-select2-selected');
          }
          return item.text + ' (' + item.count_tax + ')[' + (item.count - item.count_tax) + ']';
        },
        templateSelection: function (item, li) {
          $(li).addClass('taxonomy-admin-select2');
          if (!item.id)
          {
            return item.text;
          }
          if ($('#in-' + taxonomy + '-' + item.id + ':checked').length)
          {
            $(li).addClass('taxonomy-admin-select2-selected');
          }
          return item.text + ' (' + item.count_tax + ')[' + (item.count - item.count_tax) + ']';
        },
        ajax: {
          url: options.admin_ajax_url,
          dataType: 'json',
          type: 'POST',
          delay: 500,
          cache: true,
          data: function (params) { // page is the one-based page number tracked by Select2
            var form_data = {};
            form_data.action = 'taxonomy_admin_filter';
            form_data.taxonomy = taxonomy;
            form_data.post_id = options.post_id;
            form_data.post_type = options.post_type;
            form_data.query = params.term;
            form_data.page = params.page ? params.page : 1;
            form_data.ignore_hidden = 1;
            return form_data;
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results ? data.results : [],
              pagination: {
                more: data.more ? true : false
              }
            };
          }
        }
      }).on("select2:selecting", function (e) {
        var item = e.params.args.data;
        if (e.params.args.originalEvent && e.params.args.originalEvent.target) {
          var li = e.params.args.originalEvent.target;
          var $li = $(li);
        } else {
          var $li = $('li.select2-results__option--highlighted');
        }
        var selecting = !$li.hasClass('taxonomy-admin-select2-selected');
        var $checkbox = $('#in-' + taxonomy + '-' + item.id);
        if (selecting)
        {
          if ($checkbox.length) {
            var $parent_li = $checkbox.closest('li');
            if (!$checkbox.is(':checked')) {
              $checkbox.prop('checked', true).trigger('change');
            }
          } else
          {
            $checkbox = add_selected_option(item);
          }
          $li.addClass('taxonomy-admin-select2-selected');
        } else {
          if ($checkbox.length) {
            var $parent_li = $checkbox.closest('li');
            $parent_li.remove();
          }
          $li.removeClass('taxonomy-admin-select2-selected');
        }
        calculateSelected();
        calculateTotal();
        return false;
      }).on("select2:opening", function (e) {
        if (!is_searched) {
          is_searched = true;
          var $search = $select2_select.data('select2').dropdown.$search || $select2_select.data('select2').selection.$search;
          $search.val($search_input.val()).trigger('input');
          return false;
        }
      }).on("select2:closing", function (e) {
        is_searched = false;
      });
    }
    var is_searched = false;
    var search_style_timer;
    var total;
    var total_visible;
    function calculateTotal(number) {
      if (!number)
      {
        total_visible = $('#' + checkbox_container_ul_id + ' li:visible>label:visible').length;
      } else {
        total_visible = number;
      }
      if (total === undefined) {
        if (tax_options.select2)
        {
          total = tax_options.total;
        } else {
          total = total_visible;
        }
        $total_count_div.html(lang.total_items + '<span>' + total + '</span>');
      }
      $results_count_div.html(lang.total_results + '<span>' + total_visible + '</span>');
    }
    var calculate_selected_timer;
    function calculateSelected() {
      clearTimeout(calculate_selected_timer);
      calculate_selected_timer = setTimeout(function () {
        var total = $('#' + checkbox_container_ul_id + ' li[data-selected="1"]').length;
        $selected_count_div.html(lang.total_selected + '<span>' + total + '</span>');
      }, 500);
    }
    var updateStyle = function (event) {
      $checkbox_container.addClass('reducing_results');
      clearTimeout(search_style_timer);
      var new_value = stripDiacritics($search_input.val());
      var show_count = $search_check_count.is(':checked');
      var show_cnt = $search_check_cnt.is(':checked');
      var show_select = tax_options.select2 || (!tax_options.select2 && $search_check_select.is(':checked'));
      search_style_timer = setTimeout(function () {
        if (new_value.length || show_count || show_select || show_cnt) {
          var css = "";
          if (new_value.length) {
            var val_to_split = $.trim(new_value);
            var val_split = val_to_split.split(' ');
            for (var i = 0; i < val_split.length; i++)
            {
              var attr_selector = 'contentlc';
              var set_css = false;
              var operator = '';
              var not = false;
              var item = val_split[i].replace('+', ' ');
              if (item.indexOf('-') === 0)
              {
                // negate the statement
                not = true;
                item = item.substring(1);
              }
              if (item.indexOf('!') === 0)
              {
                // case sensitive
                attr_selector = 'content';
                item = item.substring(1);
              } else {
                item = item.toLowerCase();
              }
              if (!item.length)
              {
                continue;
              }
              if (item.indexOf('$') === 0)
              {
                // ends with
                item = item.substring(1);
                operator = '$';
                not = !not;
              } else if (item.indexOf('^') === 0)
              {
                // starts with
                item = item.substring(1);
                operator = '^';
                not = !not;
              } else if (item.indexOf('=') === 0)
              {
                // that contains word
                item = item.substring(1);
                operator = '~';
                not = !not;
              } else
              {
                // that contains string
                operator = '*';
                not = !not;
              }

              if (item.length)
              {
                css += '#' + checkbox_container_ul_id + ' li' + (not ? ':not(' : '') + '[data-' + attr_selector + operator + '="' + item + '"]' + (not ? ')' : '') + '>label{display:none;}';
              }
            }
          }
          if (show_cnt) {
            css += '#' + checkbox_container_ul_id + ' li[data-cnt="0"]>label{display:none;}';
          }
          if (show_count) {
            css += '#' + checkbox_container_ul_id + ' li:not([data-count="0"])>label{display:none;}';
          }
          if (show_select) {
            css += '#' + checkbox_container_ul_id + ' li:not([data-selected="1"])>label{display:none;}';
          }
          $search_style.html(css);
        } else {
          $search_style.empty();
        }
        calculateTotal();
        $checkbox_container.removeClass('reducing_results');
      }, 500);
    };
    $search_input.on('input', updateStyle);
    $search_check_cnt.on('change', updateStyle);
    $search_check_count.on('change', updateStyle);
    if (!tax_options.select2)
    {
      $search_check_select.on('change', updateStyle);
      $search_check_all.on('change', function () {
        $('li>label:visible input[type=checkbox]:not(:checked)', $checkbox_container).prop('checked', true).each(function () {
          $(this).closest('li').attr('data-selected', this.checked ? '1' : '0');
        });
        this.checked = false;
        calculateSelected();
      });
    }
    $search_uncheck_all.on('change', function () {
      $('li>label:visible input[type=checkbox]:checked', $checkbox_container).prop('checked', false).each(function () {
        var $this = $(this);
        var $parent_li = $this.closest('li');
        if (tax_options.select2)
        {
          $parent_li.remove();
        } else {
          $parent_li.attr('data-selected', this.checked ? '1' : '0');
        }
        if (tax_options.select2) {
          calculateTotal();
        }
      });
      this.checked = false;
      calculateSelected();
    });
    calculateTotal();
    calculateSelected();
    updateStyle();
  });
})(jQuery);