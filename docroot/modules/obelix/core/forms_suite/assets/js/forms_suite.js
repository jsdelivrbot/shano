/**
 * Created by eom on 16.10.16.
 */

(function ($, Drupal) {

    // append the default ajax process to enable form elements on ajax event.
    Drupal.Ajax.prototype.beforeSendParent = Drupal.Ajax.prototype.beforeSend;
    Drupal.Ajax.prototype.beforeSend = function (xmlhttprequest, options) {
        this.beforeSendParent(xmlhttprequest, options);
        $(this.element).prop('disabled', false);
    }
})(jQuery, Drupal);
