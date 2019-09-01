window.success = (obj = {}) => {
  Swal.fire(Object.assign({
    toast: true,
    width: 350,
    position: 'top',
    showConfirmButton: false,
    timer: 3000,
    title: 'Success!',
    type: 'success',
    text: 'it\'s a good day!'
  }, obj))
}

window.fail = (obj = {}) => {
  Swal.fire(Object.assign({
    toast: true,
    width: 350,
    position: 'top',
    showConfirmButton: false,
    timer: 3000,
    type: 'error',
    title: 'Oops...',
    text: 'Something went wrong!'
  }, obj))
}

window.modal = (obj = {}, action ) => {
  Swal.fire( Object.assign({
    toast: false,
    width: 450,
    height: 550,
    position: 'center',
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    confirmButtonColor: '#009245',
    timer: false,
    type: 'question',
    title: 'Confirm action',
    text: 'Are you sure to want to delete?',
  }, obj)).then( ( {value} ) => {
    if ( value ) {
      action();
    }
  })
}
