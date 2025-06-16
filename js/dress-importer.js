jQuery(document).ready(function ($) {
	const form = $('#dress-import-form');
	const progressContainer = $('#import-progress');
	const progressBar = $('.progress-bar-fill');
	const progressStatus = $('.progress-status');
	let isImporting = false;

	form.on('submit', function (e) {
		e.preventDefault();

		if (isImporting) {
			return;
		}

		const fileInput = $('#dress-xml')[0];
		if (!fileInput.files.length) {
			alert('Пожалуйста, выберите XML файл.');
			return;
		}

		const formData = new FormData();
		formData.append('action', 'import_dresses');
		formData.append('nonce', loveforeverDressImporter.nonce);
		formData.append('file', fileInput.files[0]);

		isImporting = true;
		form.hide();
		progressContainer.show();
		progressStatus.text(loveforeverDressImporter.importing);

		importDresses(formData);
	});

	function importDresses(
		formData,
		currentRow = 0,
		imported = 0,
		skipped = 0,
		totalRows = 0
	) {
		if (currentRow > 0) {
			formData.append('current_row', currentRow);
			formData.append('imported', imported);
			formData.append('skipped', skipped);
			formData.append('total_rows', totalRows);
		}

		$.ajax({
			url: loveforeverDressImporter.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					const data = response.data;

					if (data.complete) {
						progressStatus.text(loveforeverDressImporter.complete);
						progressBar.css('width', '100%');
						isImporting = false;
						form.show();
						return;
					}

					progressBar.css('width', data.progress + '%');
					progressStatus.text(data.message);

					// Continue with next row
					importDresses(
						formData,
						data.current_row,
						data.imported,
						data.skipped,
						data.total
					);
				} else {
					progressStatus.text(loveforeverDressImporter.error);
					isImporting = false;
					form.show();
				}
			},
			error: function () {
				progressStatus.text(loveforeverDressImporter.error);
				isImporting = false;
				form.show();
			},
		});
	}
});
