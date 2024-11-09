if (old) {
    $.ajax({
        type: "POST",
        url: "{{ route('scaleup.getByID') }}",
        data: {
            _token: token,
            id: old,
        },
        dataType: "JSON",
        success: function (response) {
            let option = new Option(
                response.header.product_code +
                    " - " +
                    response.header.material_code +
                    " - " +
                    response.header.material_description +
                    " - " +
                    response.header.doc_number,
                old,
                true,
                true
            );
            $(el).append(option).trigger("change");
        },
        error: function (request, status, error) {
            console.log(error);
        },
    });
}
