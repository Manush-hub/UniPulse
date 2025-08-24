// Feather icons
if (window.feather) feather.replace();

// Toggle public/private field
function toggleField(btn) {
  btn.classList.toggle('active');
  const icon = btn.querySelector('i');
  if (btn.classList.contains('active')) {
    icon.setAttribute('data-feather', 'eye-off');
  } else {
    icon.setAttribute('data-feather', 'eye');
  }
  if (window.feather) feather.replace();
}

// Cover photo upload
function uploadCover() {
  document.getElementById('coverInput').click();
}
function changeCover(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('coverImg').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Profile photo upload
function uploadProfile() {
  document.getElementById('profileInput').click();
}
function changeProfile(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('profileImg').src = e.target.result;
      document.querySelector('.public-profile-pic').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Preview as Public (scrolls to preview)
function previewProfile() {
  const preview = document.querySelector('.public-preview');
  if (preview) preview.scrollIntoView({ behavior: 'smooth' });
}

// Add tag functionality
document.addEventListener('DOMContentLoaded', () => {
  const tagInput = document.querySelector('.tag-input');
  const tagsContainer = document.querySelector('.tags');
  tagInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
      e.preventDefault();
      const tag = document.createElement('span');
      tag.className = 'tag';
      tag.textContent = this.value.trim();
      tagsContainer.insertBefore(tag, tagInput);
      this.value = '';
    }
  });
});