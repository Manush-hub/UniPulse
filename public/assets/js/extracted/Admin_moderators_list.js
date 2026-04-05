let currentDeleteUrl = '';
        
        async function confirmModeratorDeletion(moderatorId, moderatorName, universityName) {
            try {
                // Check for pending approvals
                const response = await fetch(`/unipulse/public/admin/moderators/check_pending/${moderatorId}`);
                const data = await response.json();
                
                const modal = document.getElementById('deleteModal');
                const message = document.getElementById('modalMessage');
                const warningBox = document.getElementById('pendingWarning');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                
                message.innerHTML = `Are you sure you want to delete moderator <strong>${moderatorName}</strong> from <strong>${universityName}</strong>?<br><br>This action cannot be undone.`;
                
                if (data.hasPending && data.pendingCount > 0) {
                    warningBox.style.display = 'flex';
                    warningBox.innerHTML = `<i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Warning:</strong> This moderator has <strong>${data.pendingCount}</strong> pending publisher approval(s) 
                            for ${universityName}. These approvals need to be resolved first.
                        </div>`;
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Cannot Delete';
                    currentDeleteUrl = '';
                } else {
                    warningBox.style.display = 'none';
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete Moderator';
                    currentDeleteUrl = `/unipulse/public/admin/moderators/deactivate/${moderatorId}`;
                }
                
                modal.style.display = 'block';
                return false; // Prevent default action
            } catch (error) {
                console.error('Error checking pending approvals:', error);
                // Fallback to simple confirmation with server-side check
                const message = `Are you sure you want to delete moderator ${moderatorName}?\n\n` +
                              `Note: If this moderator has pending approvals, the deletion will be blocked by the server.\n\n` +
                              `This action cannot be undone.`;
                return confirm(message);
            }
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        function proceedWithDeletion() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl;
            }
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });