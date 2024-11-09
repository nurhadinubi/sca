$(document).ready(function () {
    // const oldUser = @json(old('user-select'));
    // console.log(oldUser);
    $("#user-select").select2({
        placeholder: "Select a user",
        ajax: {
            url: "/user/search",
            // url: '{{ route('users.search') }}',
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.name} - ${item.email}`,
                            id: item.id,
                        };
                    }),
                };
            },
            cache: true,
        },
    });

    // if (oldUser) {
    //     $.ajax({
    //         url: '/user/search',
    //         data: {
    //             id: oldUser
    //         },
    //         success: function(data) {
    //             const user = data.find(user => user.id == oldUser);
    //             const option = new Option(`${user.name} - ${user.email}`, user.id, true, true);
    //             $('#user-select').append(option).trigger('change');

    //             // Fetch user permissions
    //             $.ajax({
    //                 url: '{{ url('master/permission/user') }}/' + oldUser,
    //                 type: 'GET',
    //                 success: function(response) {
    //                     let active = $('#active-permission');
    //                     let open = $('#open-permission');
    //                     active.html('')
    //                     open.html('')
    //                     $.each(response.active, function(index, permission) {
    //                         active.append(
    //                             `<div>
    //                                 <input class="form-check-input" type="checkbox" id="active-${permission.id}" name="actives[]" value="${permission.id}" checked>
    //                                 <label for="active-${permission.id}">${permission.name}</label>
    //                             </div>`
    //                         );
    //                     });

    //                     if (response.nonactive.length >= 1) {
    //                         $.each(response.nonactive, function(index, permission) {
    //                             open.append(
    //                                 `<div>
    //                                     <input class="form-check-input" type="checkbox" id="open-${permission.id}" name="opens[]" value="${permission.id}">
    //                                     <label for="open-${permission.id}">${permission.name}</label>
    //                                 </div>`
    //                             );
    //                         });
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // }

    $("#user-select").on("select2:select", function (e) {
        var userId = e.params.data.id;

        $.ajax({
            url: "/master/permission/user/" + userId,
            type: "GET",
            success: function (response) {
                let active = $("#active-permission");
                let open = $("#open-permission");
                active.html("");
                open.html("");
                $.each(response.active, function (index, permission) {
                    active.append(
                        `<div>
                        <input class="form-check-input" type="checkbox" id="active-${permission.id}" name="actives[]" value="${permission.id}" checked>
                        <label for="active-${permission.id}">${permission.name}</label>
                    </div>`
                    );
                });

                if (response.nonactive.length >= 1) {
                    $.each(response.nonactive, function (index, permission) {
                        open.append(
                            `<div>
                        <input class="form-check-input" type="checkbox" id="open-${permission.id}" name="opens[]" value="${permission.id}">
                        <label for="open-${permission.id}">${permission.name}</label>
                    </div>`
                        );
                    });
                }
            },
        });
    });
    $("#btn-submit").attr("disabled", true);

    if ($("#user-select").val() != "" || $("#user-select").val() != null) {
        $("#btn-submit").prop("disabled", false);
    } else {
        $("#btn-submit").attr("disabled", true);
    }
});
