$().ready(function(){
	$('.radio').bind('click',
		function(){
			if($('#site:checked').val() == 'site') {
				$('#db_params').hide();
			} else 
				$('#db_params').show();
		}
	);

    $('.count_like').bind('click',
        function(){
            var targ = $(this);
            $.post(
                "/index/addLike",
                {'photo_id': $(this).attr('data-id') },
                function(data) {
                    targ.text(data);
                    targ.removeClass('badge-important').addClass('badge-success');
                    targ.unbind();
                })
        }
    );
	
	$('.tab-pane .filter-tag .label').bind('click', 
		function(){
			if($(this).hasClass('label-inverse')) {
                if($('.tab-pane .filter-tag .label-success').length == 5) {
                    alert('Установлено максимальное количество тегов фильтра!');
                    return;
                }

                $(this).removeClass('label-inverse').addClass('label-success');
			}
			else if($(this).hasClass('label-success')) {
				$(this).removeClass('label-success').addClass('label-inverse');
			}
			document.location.href = $(this).attr('data-tag');
		}
	);
	
	$('.tab-pane .filter-tag-fail .label').bind('click', 
		function(){
			if($(this).hasClass('label-important')) {
				$(this).removeClass('label-important').addClass('label-inverse');
			}
			else if($(this).hasClass('label-inverse')) {
                if($('.tab-pane .filter-tag-fail .label-important').length == 3) {
                    alert('Установлено максимальное количество тегов-исключений!');
                    return;
                }

                $(this).removeClass('label-inverse').addClass('label-important');
			}
			document.location.href = $(this).attr('data-tag');
		}
	);
})