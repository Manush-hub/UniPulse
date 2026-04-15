<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Create Moderator</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Admin_moderator_create.css">
</head>
<body>
    <!-- Header -->
    <!-- <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/admin/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/admin/dashboard">Dashboard</a>
                <a href="/unipulse/public/admin/moderators" class="active">Moderators</a>
                <a href="/unipulse/public/admin/admins">Admins</a>
            </nav>
            <div class="header-actions">
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/admin.png" alt="Admin" class="admin-avatar">
                    <div class="user-info">
                        <span class="username"><?php echo htmlspecialchars($user['name']); ?></span>
                        <span class="user-role">System Administrator</span>
                    </div>
                </div>
            </div>
        </div>
    </header> -->

    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'moderator_create'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="create-moderator">
            <div class="container">
                <div class="form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-user-shield"></i> Create New Moderator</h1>
                        <p>Add a new moderator to help manage the platform</p>
                    </div>

                    <form method="POST" action="/unipulse/public/admin/moderator_create">
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="required-asterisk">*</span></label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['full_name']) ? htmlspecialchars($old_data['full_name']) : ''; ?>" 
                                   required>
                            <div class="validation-error" id="full_name_error"></div>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="form-error"><?php echo $errors['full_name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required-asterisk">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>" 
                                   required>
                            <div class="validation-error" id="email_error"></div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="form-error"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number (Optional)</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   placeholder="e.g., 0771234567 or +94771234567"
                                   value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>">
                            <div class="validation-error" id="phone_error"></div>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="form-error"><?php echo $errors['phone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="university">University <span class="required-asterisk">*</span></label>
                            <select id="university" 
                                    name="university" 
                                    class="form-control" 
                                    required>
                                <option value="">Select University</option>
                                <!-- State Universities -->
                                <optgroup label="State Universities">
                                    <option value="university-of-colombo" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-colombo') ? 'selected' : '' ?>>University of Colombo</option>
                                    <option value="university-of-peradeniya" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-peradeniya') ? 'selected' : '' ?>>University of Peradeniya</option>
                                    <option value="university-of-sri-jayewardenepura" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-sri-jayewardenepura') ? 'selected' : '' ?>>University of Sri Jayewardenepura</option>
                                    <option value="university-of-kelaniya" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-kelaniya') ? 'selected' : '' ?>>University of Kelaniya</option>
                                    <option value="university-of-moratuwa" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-moratuwa') ? 'selected' : '' ?>>University of Moratuwa</option>
                                    <option value="university-of-jaffna" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-jaffna') ? 'selected' : '' ?>>University of Jaffna</option>
                                    <option value="university-of-ruhuna" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-ruhuna') ? 'selected' : '' ?>>University of Ruhuna</option>
                                    <option value="eastern-university" <?= (isset($old_data['university']) && $old_data['university'] === 'eastern-university') ? 'selected' : '' ?>>Eastern University, Sri Lanka</option>
                                    <option value="south-eastern-university" <?= (isset($old_data['university']) && $old_data['university'] === 'south-eastern-university') ? 'selected' : '' ?>>South Eastern University of Sri Lanka</option>
                                    <option value="rajarata-university" <?= (isset($old_data['university']) && $old_data['university'] === 'rajarata-university') ? 'selected' : '' ?>>Rajarata University of Sri Lanka</option>
                                    <option value="sabaragamuwa-university" <?= (isset($old_data['university']) && $old_data['university'] === 'sabaragamuwa-university') ? 'selected' : '' ?>>Sabaragamuwa University of Sri Lanka</option>
                                    <option value="wayamba-university" <?= (isset($old_data['university']) && $old_data['university'] === 'wayamba-university') ? 'selected' : '' ?>>Wayamba University of Sri Lanka</option>
                                    <option value="uva-wellassa-university" <?= (isset($old_data['university']) && $old_data['university'] === 'uva-wellassa-university') ? 'selected' : '' ?>>Uva Wellassa University</option>
                                    <option value="open-university" <?= (isset($old_data['university']) && $old_data['university'] === 'open-university') ? 'selected' : '' ?>>Open University of Sri Lanka</option>
                                    <option value="buddhist-and-pali-university" <?= (isset($old_data['university']) && $old_data['university'] === 'buddhist-and-pali-university') ? 'selected' : '' ?>>Buddhist and Pali University of Sri Lanka</option>
                                </optgroup>
                                <!-- Private Universities -->
                                <optgroup label="Private Universities">
                                    <option value="sliit" <?= (isset($old_data['university']) && $old_data['university'] === 'sliit') ? 'selected' : '' ?>>Sri Lanka Institute of Information Technology (SLIIT)</option>
                                    <option value="nsbm" <?= (isset($old_data['university']) && $old_data['university'] === 'nsbm') ? 'selected' : '' ?>>NSBM Green University</option>
                                    <option value="cinec" <?= (isset($old_data['university']) && $old_data['university'] === 'cinec') ? 'selected' : '' ?>>CINEC Campus</option>
                                    <option value="apiit" <?= (isset($old_data['university']) && $old_data['university'] === 'apiit') ? 'selected' : '' ?>>Asia Pacific Institute of Information Technology (APIIT)</option>
                                    <option value="metropolitan-campus" <?= (isset($old_data['university']) && $old_data['university'] === 'metropolitan-campus') ? 'selected' : '' ?>>KIU (Kaatsu International University)</option>
                                </optgroup>
                            </select>
                            <div class="validation-error" id="university_error"></div>
                            <?php if (isset($errors['university'])): ?>
                                <div class="form-error"><?php echo $errors['university']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="required-asterisk">*</span></label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required>
                            <div class="validation-error" id="password_error"></div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="form-error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="permission-group">
                                <div class="permission-item">
                                    <input type="checkbox" id="view_events" name="permissions[view_events]" value="1" checked>
                                    <label for="view_events">View Events</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="edit_events" name="permissions[edit_events]" value="1" checked>
                                    <label for="edit_events">Edit Events</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="view_users" name="permissions[view_users]" value="1" checked>
                                    <label for="view_users">View Users</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="moderate_content" name="permissions[moderate_content]" value="1" checked>
                                    <label for="moderate_content">Moderate Content</label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field to store university name -->
                        <input type="hidden" id="university_name" name="university_name" value="">

                        <?php if (isset($errors['general'])): ?>
                            <div class="form-error" style="margin-bottom: 1rem;"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Moderator
                            </button>
                            <a href="/unipulse/public/admin/moderators_list" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="<?php echo ROOT ?>/assets/js/extracted/Admin_moderator_create.js"></script>
</body>
</html>
