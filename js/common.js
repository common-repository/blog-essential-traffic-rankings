function bpetr_confirmUrlAction(route) {
	if (confirm('Can you please confirm you want to proceed ?')) {
		document.location.href = route;
	}
}

function bpetr_checkGoogleAuthError(ajaxErr) {
	if (ajaxErr.responseJSON !== undefined && ajaxErr.responseJSON.detail !== undefined) {
        let ggErr = ajaxErr.responseJSON.detail;
        if (
        	true === ggErr.includes("googleapis")
        	|| true === ggErr.includes("invalid_grant")
        ) {
            return '<div class="mb-3 mt-2 bg-light p-3">' +
            	'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ' +
            	'Your Google token is no longer valid.<br />Please unauthorize, then re-authorize your website.' +
            	'</div>';
        }
    }

    return '<div class="mb-3 mt-2 bg-light p-3">' +
    	'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Error while loading data.<br />' +
    	'Error code: <b>' + ajaxErr.status+ '</b>. Error message: <b>' + ajaxErr.statusText + '</b>' +
    	'</div>';
}
