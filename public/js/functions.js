Object.deepCopy = function deepCopy(source) {
    return JSON.parse(JSON.stringify(source));
}

function makeid(length) {
    var length = length || 16;
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

const alerts = {
    success: (text) => {
        return Swal(
            'Success!',
            text,
            'success'
        )
    },
    error: (text) => {
        return Swal(
            'Failed!',
            text,
            'error'
        )
    },
    confirm: (text) => {
        return Swal.fire({
            title: 'Are you sure?',
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        })
    }
}
