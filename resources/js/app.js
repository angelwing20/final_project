import "./bootstrap";

// 引入 select2 的 JS 和样式
import "select2"; // JS 功能（让你可以用 $('.select2').select2()）
import "select2/dist/css/select2.min.css"; // 样式
import "select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css"; // bootstrap 5 的主题样式

window.resetFilterForm = function (formSelector) {
    const $form = $(formSelector);

    $form.find('input[type="text"]').val('');

    $form.find('select').each(function () {
        const $select = $(this);
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.val(null).trigger('change');
        } else {
            $select.val('');
        }
    });
};
