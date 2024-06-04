$('.image_upload_btn').on('click', (e) => {
    $(e.currentTarget).closest('.form-group').find('.image_upload').trigger('click');
});

$('.image_upload').on('change', (e) => {
    const reader = new FileReader();
    reader.onload = (event) => {
        $(e.currentTarget).closest('.form-group').find('#is_image').val(e.currentTarget.files[0].name);
        $(e.currentTarget).closest('.form-group').find('.preview_image').prop('src', event.target.result);
        $(e.currentTarget).closest('.form-group').find('.image_upload_btn').addClass('d-none')
        $(e.currentTarget).closest('.form-group').find('.preview_image').removeClass('d-none')
        $(e.currentTarget).closest('.form-group').find('.image_delete_btn').parent('div').removeClass('d-none');
    }
    reader.readAsDataURL(e.currentTarget.files[0]);
});

$('.image_delete_btn').on('click', (e) => {
    $(e.currentTarget).closest('.form-group').find('#is_image').val('');
    $(e.currentTarget).closest('.form-group').find('.image_upload').val('');
    $(e.currentTarget).closest('.form-group').find('.preview_image').prop('src', '#');
    $(e.currentTarget).closest('.form-group').find('.preview_image').addClass('d-none')
    $(e.currentTarget).closest('.form-group').find('.image_delete_btn').parent('div').addClass('d-none');
    $(e.currentTarget).closest('.form-group').find('.image_upload_btn').removeClass('d-none');
});
