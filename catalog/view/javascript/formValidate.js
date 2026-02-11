
     $(document).on('input', '.phoneValidate', function() {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
  });

  // Restrict postcode to 1–4 digits only
  
      $(document).on('input', '.postcodeValidate', function() {
   this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
  });

  // Year: 4 digits only, numbers
  $(document).on('input', '.yearValidate', function() {
  // Remove all non-numeric characters and limit to 4 digits
  this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
});


  // Item weight: allow decimals
$('.validateDecimal').on('input', function() {
  let val = this.value;

  // Allow only numbers and a single dot
  val = val.replace(/[^0-9.]/g, '');

  // If more than one dot, keep only the first
  let parts = val.split('.');
  if (parts.length > 2) {
    val = parts[0] + '.' + parts.slice(1).join('');
  }

  // Prevent multiple leading zeros (e.g., 00.5 → 0.5)
  if (val.startsWith('00')) {
    val = val.replace(/^0+/, '0');
  } else if (val.startsWith('0') && val.length > 1 && val[1] !== '.') {
    val = val.replace(/^0+/, '');
  }

  this.value = val;
});
