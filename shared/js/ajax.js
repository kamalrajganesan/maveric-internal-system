

export function ajax(url, data_param, sender) {
    $.ajax({
        url: url,
        type: "post",
        data: { 
            sender: sender,
            param: data_param
        },
        dataType: "json",
        success: function (response) {
            return {
                status: true,
                response: response
            }
        },
        error: function (response) {
            customToast(response, "error")
            return {
                status: false,
                response: response
            }
        },
    })
}