(function ($) {
    "use strict";

    if ($("#myhome_attributes_box").length > 0 && $("#acf-myhome_estate .inside.acf-fields").length > 0) {
        $("#acf-myhome_estate .acf-field-myhome-estate-tab-general").after($("#mh-admin-attributes").html());
        $("#myhome_attributes_box").remove();
    }

    if ($(".redux-action_bar").length > 0) {
        $("#redux_save").after($("#myhome-clear-cache"));
        $("#myhome-clear-cache").show();
    }
})(jQuery);