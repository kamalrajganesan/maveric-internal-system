function formatDate(dateString) {
    const date = new Date(dateString);
  
    // Extract the components
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const year = date.getFullYear();
    
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
  
    // Determine AM or PM
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? String(hours).padStart(2, '0') : '12'; // Handle midnight and noon
  
    // Combine into the desired format
    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
}