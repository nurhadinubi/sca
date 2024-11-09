$(document).ready(function () {
    const path = $("#product-code").attr("data-getmaterial");

    console.log("url", path);
    $("#material").select2({
        placeholder: "Pilih material atau cari",
        theme: "bootstrap-5",
        ajax: {
            url: path,
            type: "GET",
            dataType: "json",
            casesensitive: false,
            processResults: (data) => {
                return {
                    results: data.map((res) => {
                        return {
                            text: ` ${res.id} - ${
                                res.material_code
                                    ? res.material_code
                                    : "No SAP Code"
                            } - ${res.material_description}`,
                            id: res.id,
                        };
                    }),
                };
            },
        },
    });
});
