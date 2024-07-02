$(document).on('submit', '.form-store', function() {
    if (!confirm('情報を登録してもよろしいですか？')) {
        return false;
    }
});

$(document).on('submit', '.form-update', function() {
    if (!confirm('情報を更新してもよろしいですか？')) {
        return false;
    }
});

$(document).on('submit', '.form-destroy', function() {
    if (!confirm('削除してもよろしいですか？')) {
        return false;
    }
});

$(document).on('submit', '.form-logout', function() {
    if (!confirm('ログアウトしますか？')) {
        return false;
    }
});

$(document).on('submit', '.form-cancel', function() {
    if (!confirm('キャンセルしますか？')) {
        return false;
    }
});

$('#search_box').find(':input').each(function() {
    if ($(this).val() != '') {
        if ($(this).prop('type') == 'radio' && ! $(this).prop('checked')) {
            return true;
        }
        $('#search_box').find('.card-header').removeClass('collapsed');
        $('#search_box').find('.collapse').addClass('show');
        return false;
    }
});

$(document).on('click', '.form-reset', function() {
    $(this).closest('form').find(':input').val('').end().find(':checked').prop('checked', false);
    $(this).closest('form').find('.default').prop('checked', true);
    $(this).closest('form').find(':radio').checked = true;
    $(this).closest('form').find('select.select2-single').val('').trigger('change');
});

$('[data-toggle="tooltip"]').tooltip();

$('ul.vertical-menu').find('li').each(function() {
    if (window.location.pathname.includes(this.id)) {
        $(this).closest('ul.vertical-menu > li').addClass('active');
        $(this).addClass('active').children('a').addClass('active');
    }
});
