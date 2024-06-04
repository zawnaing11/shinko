// categories select 2
$('.categories').select2({
    placeholder: '選択してください',
    tokenSeparators: [','],
    language: {
        noResults: (e) => '情報がありません。',
        searching: (e) => '検索中',
    }
});

// tags select 2
$('.tags').select2({
    placeholder: '選択してください',
    tokenSeparators: [','],
    language: {
        noResults: (e) => '情報がありません。',
        searching: (e) => '検索中',
    }
});

// related select 2
$('.related').select2({
    placeholder: '選択してください',
    tokenSeparators: [','],
    language: {
        noResults: (e) => '情報がありません。',
        searching: (e) => '検索中',
        maximumSelected: function () {
            return '3つまで選択可能';
        }
    },
    maximumSelectionLength: 3,
    ajax: {
        url: '/admin/knowhow/search',
        data: function (params) {
            return {
              q: params.term,
              id: data.id
            }
        },
        processResults: (data) => {
            return {
                results: data.map((item) => {
                    return {
                        id: item.id,
                        text: item.post_title
                    }
                })
            }
        }
    }
});

$('.select2-single').select2({
    language: {
        noResults: (e) => '情報がありません。'
    },
});

// selcet2's placeholder binding in index for collapse's bug
$('.collapse').on('show.bs.collapse', (e) => {
    $('.select2-search__field').css('width', '100%');
})
