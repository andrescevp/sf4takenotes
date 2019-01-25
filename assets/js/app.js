import Bloodhound from "bloodhound-js";

var $ = require('jquery');
window.$ = $;
global.$ = $;
global.jQuery = $;
window.jQuery = $;

// loads the Bootstrap jQuery plugins
import 'bootstrap-sass/assets/javascripts/bootstrap/transition.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/alert.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/dropdown.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal.js';
import 'jquery'

// loads the code syntax highlighting library
import './highlight.js';

import 'typeahead.js';
import 'bootstrap-tagsinput';


var $input = $('input[data-toggle="tagsinput"]');
if ($input.length) {
    var source = new Bloodhound({
        local: $input.data('tags'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        datumTokenizer: Bloodhound.tokenizers.whitespace
    });
    source.initialize();

    $input.tagsinput({
        trimValue: true,
        focusClass: 'focus',
        typeaheadjs: {
            name: 'tags',
            source: source.ttAdapter()
        }
    });
}

$(document).on('submit', 'form[data-confirmation]', function (event) {
    var $form = $(this),
        $confirm = $('#confirmationModal');

    if ($confirm.data('result') !== 'yes') {
        //cancel submit event
        event.preventDefault();

        $confirm
            .off('click', '#btnYes')
            .on('click', '#btnYes', function () {
                $confirm.data('result', 'yes');
                $form.find('input[type="submit"]').attr('disabled', 'disabled');
                $form.submit();
            })
            .modal('show');
    }
});