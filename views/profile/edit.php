<?php
$pageTitle = 'Edit Profile - GymBro';
require __DIR__ . '/../partials/header.php';

$errors = $_SESSION['profile_errors'] ?? [];
unset($_SESSION['profile_errors']);

// workout_styles is already decoded in the controller
$workoutStyles = is_array($user['workout_styles']) ? $user['workout_styles'] : (json_decode($user['workout_styles'] ?? '[]', true) ?? []);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg card-profile">
            <div class="card-body p-4">
                <h2 class="mb-4" style="color: var(--primary);">
                    <i class="bi bi-pencil"></i> Edit Profile
                </h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= escape($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/profile/update">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <h5 class="mb-3" style="color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        <i class="bi bi-person"></i> Basic Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                name="name" 
                                value="<?= escape($user['name'] ?? '') ?>"
                                required
                            >
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age *</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="age" 
                                name="age" 
                                value="<?= escape($user['age'] ?? '') ?>"
                                min="13"
                                max="100"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pronouns" class="form-label">Pronouns *</label>
                            <select class="form-select" id="pronouns" name="pronouns" required>
                                <option value="he/him" <?= ($user['pronouns'] ?? '') === 'he/him' ? 'selected' : '' ?>>He/Him</option>
                                <option value="she/her" <?= ($user['pronouns'] ?? '') === 'she/her' ? 'selected' : '' ?>>She/Her</option>
                                <option value="they/them" <?= ($user['pronouns'] ?? '') === 'they/them' ? 'selected' : '' ?>>They/Them</option>
                                <option value="prefer_not_to_say" <?= ($user['pronouns'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="nonbinary" <?= ($user['gender'] ?? '') === 'nonbinary' ? 'selected' : '' ?>>Nonbinary</option>
                                <option value="prefer_not_to_say" <?= ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            value="<?= escape($user['email'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <h5 class="mb-3 mt-4" style="color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        <i class="bi bi-lightning-charge"></i> Fitness Information
                    </h5>
                    
                    <div class="mb-3">
                        <label for="activity_level" class="form-label">Activity Level</label>
                        <select class="form-select" id="activity_level" name="activity_level">
                            <option value="" <?= empty($user['activity_level']) ? 'selected' : '' ?>>Select activity level...</option>
                            <option value="not_very_active" <?= ($user['activity_level'] ?? '') === 'not_very_active' ? 'selected' : '' ?>>Not Very Active</option>
                            <option value="kinda_active" <?= ($user['activity_level'] ?? '') === 'kinda_active' ? 'selected' : '' ?>>Kinda Active</option>
                            <option value="super_gymbro" <?= ($user['activity_level'] ?? '') === 'super_gymbro' ? 'selected' : '' ?>>Super Gymbro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Workout Styles *</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="workout_styles[]" value="calisthenics" id="ws1" <?= in_array('calisthenics', $workoutStyles) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ws1">Calisthenics</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="workout_styles[]" value="weightlifting" id="ws2" <?= in_array('weightlifting', $workoutStyles) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ws2">Weightlifting</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="workout_styles[]" value="cardio" id="ws3" <?= in_array('cardio', $workoutStyles) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ws3">Cardio</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="workout_styles[]" value="athletic" id="ws4" <?= in_array('athletic', $workoutStyles) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ws4">Athletic</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3 mt-4" style="color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        <i class="bi bi-geo-alt"></i> Location & Bio
                    </h5>
                    
                    <div class="mb-3">
                        <label for="short_bio" class="form-label">Bio (Optional)</label>
                        <textarea 
                            class="form-control" 
                            id="short_bio" 
                            name="short_bio" 
                            rows="4"
                            maxlength="500"
                            placeholder="Tell others about yourself, your fitness goals, what you're looking for in a workout partner..."
                        ><?= escape($user['short_bio'] ?? '') ?></textarea>
                        <div class="form-text">Max 500 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="home_gym" class="form-label">Home Gym (Optional)</label>
                        <div class="position-relative">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="home_gym" 
                                name="home_gym" 
                                value="<?= escape($user['home_gym'] ?? '') ?>"
                                placeholder="Start typing to search for gyms..."
                                maxlength="255"
                                autocomplete="off"
                            >
                            <div id="gym-suggestions" class="autocomplete-dropdown"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City (Optional)</label>
                        <div class="position-relative">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="city" 
                                name="city" 
                                value="<?= escape($user['city'] ?? '') ?>"
                                placeholder="Start typing to search for cities..."
                                maxlength="100"
                                autocomplete="off"
                            >
                            <div id="city-suggestions" class="autocomplete-dropdown"></div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3 mt-4" style="color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        <i class="bi bi-heart"></i> Partner Preferences
                    </h5>

                    <div class="mb-4">
                        <label class="form-label">Partner Gender Preferences (Optional)</label>
                        <div class="form-text mb-3">Leave empty to see all genders. Click to select.</div>
                        <div class="gender-boxes">
                            <label class="gender-box <?= in_array('male', $user['preferred_partner_genders'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="preferred_partner_genders[]" value="male" <?= in_array('male', $user['preferred_partner_genders'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-gender-male"></i>
                                <span>Male</span>
                            </label>
                            <label class="gender-box <?= in_array('female', $user['preferred_partner_genders'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="preferred_partner_genders[]" value="female" <?= in_array('female', $user['preferred_partner_genders'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-gender-female"></i>
                                <span>Female</span>
                            </label>
                            <label class="gender-box <?= in_array('nonbinary', $user['preferred_partner_genders'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="preferred_partner_genders[]" value="nonbinary" <?= in_array('nonbinary', $user['preferred_partner_genders'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-gender-ambiguous"></i>
                                <span>Nonbinary</span>
                            </label>
                            <label class="gender-box <?= in_array('prefer_not_to_say', $user['preferred_partner_genders'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="preferred_partner_genders[]" value="prefer_not_to_say" <?= in_array('prefer_not_to_say', $user['preferred_partner_genders'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-question-circle"></i>
                                <span>Any</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                        <a href="/profile" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.autocomplete-dropdown.show {
    display: block;
}

.autocomplete-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid var(--border);
    transition: all 0.2s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover,
.autocomplete-item.active {
    background: rgba(255, 68, 68, 0.1);
    color: var(--primary);
}

.autocomplete-item .item-name {
    font-weight: 600;
    color: var(--text);
}

.autocomplete-item .item-address {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 2px;
}

.autocomplete-item:hover .item-name,
.autocomplete-item.active .item-name {
    color: var(--primary);
}

.autocomplete-loading {
    padding: 15px;
    text-align: center;
    color: var(--text-muted);
}

.autocomplete-no-results {
    padding: 15px;
    text-align: center;
    color: var(--text-muted);
}

/* Gender preference boxes */
.gender-boxes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

@media (max-width: 768px) {
    .gender-boxes {
        grid-template-columns: repeat(2, 1fr);
    }
}

.gender-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1rem;
    background: var(--bg-darker);
    border: 2px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.gender-box input[type="checkbox"] {
    display: none;
}

.gender-box i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--text-muted);
    transition: all 0.3s ease;
}

.gender-box span {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    transition: all 0.3s ease;
}

.gender-box:hover {
    border-color: var(--primary);
    background: rgba(255, 68, 68, 0.05);
}

.gender-box:hover i,
.gender-box:hover span {
    color: var(--primary);
}

.gender-box.selected {
    border-color: var(--primary);
    background: rgba(255, 68, 68, 0.1);
    box-shadow: 
        0 0 20px rgba(255, 68, 68, 0.3),
        0 0 40px rgba(255, 68, 68, 0.2),
        inset 0 0 20px rgba(255, 68, 68, 0.1);
}

.gender-box.selected i {
    color: var(--primary);
    filter: drop-shadow(0 0 8px rgba(255, 68, 68, 0.8));
}

.gender-box.selected span {
    color: var(--primary);
    text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
}
</style>

<script>
// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Gym autocomplete using OpenStreetMap Nominatim
class Autocomplete {
    constructor(inputId, suggestionsId, type) {
        this.input = document.getElementById(inputId);
        this.suggestions = document.getElementById(suggestionsId);
        this.type = type; // 'gym' or 'city'
        this.activeIndex = -1;
        this.results = [];
        
        this.init();
    }
    
    init() {
        this.input.addEventListener('input', debounce((e) => this.search(e.target.value), 300));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.suggestions.classList.add('show');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.suggestions.contains(e.target)) {
                this.suggestions.classList.remove('show');
            }
        });
    }
    
    async search(query) {
        if (query.length < 2) {
            this.suggestions.classList.remove('show');
            return;
        }
        
        this.suggestions.innerHTML = '<div class="autocomplete-loading"><i class="bi bi-arrow-repeat"></i> Searching...</div>';
        this.suggestions.classList.add('show');
        
        try {
            let url;
            if (this.type === 'gym') {
                // Search for the exact query - works for gyms, recreation centers, wellness centers, etc.
                // Don't append keywords so "FIU Wellness and Recreation Center" finds exact matches
                url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=10&addressdetails=1`;
            } else {
                // Search for cities
                url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=8&addressdetails=1&featuretype=city`;
            }
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'User-Agent': 'GymBro App'
                }
            });
            
            if (!response.ok) throw new Error('Search failed');
            
            const data = await response.json();
            this.results = data;
            this.renderResults(data);
            
        } catch (error) {
            console.error('Search error:', error);
            this.suggestions.innerHTML = '<div class="autocomplete-no-results">Search unavailable. Please type manually.</div>';
        }
    }
    
    renderResults(results) {
        if (results.length === 0) {
            this.suggestions.innerHTML = '<div class="autocomplete-no-results">No results found. You can still type manually.</div>';
            return;
        }
        
        this.suggestions.innerHTML = results.map((item, index) => {
            let name, address;
            
            if (this.type === 'gym') {
                name = item.name || item.display_name.split(',')[0];
                address = item.display_name.split(',').slice(1, 4).join(',').trim();
            } else {
                // For cities, format as "City, State/Country"
                const addr = item.address || {};
                name = addr.city || addr.town || addr.village || addr.municipality || item.name || item.display_name.split(',')[0];
                const state = addr.state || addr.region || '';
                const country = addr.country || '';
                address = [state, country].filter(Boolean).join(', ');
            }
            
            return `
                <div class="autocomplete-item" data-index="${index}">
                    <div class="item-name"><i class="bi bi-${this.type === 'gym' ? 'building' : 'geo-alt'}"></i> ${this.escapeHtml(name)}</div>
                    <div class="item-address">${this.escapeHtml(address)}</div>
                </div>
            `;
        }).join('');
        
        // Add click handlers
        this.suggestions.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', () => this.selectItem(parseInt(item.dataset.index)));
        });
        
        this.activeIndex = -1;
    }
    
    selectItem(index) {
        const item = this.results[index];
        if (!item) return;
        
        if (this.type === 'gym') {
            // For gyms, use name + partial address
            const name = item.name || item.display_name.split(',')[0];
            const city = item.address?.city || item.address?.town || '';
            this.input.value = city ? `${name}, ${city}` : name;
        } else {
            // For cities, format as "City, State"
            const addr = item.address || {};
            const city = addr.city || addr.town || addr.village || addr.municipality || item.name || item.display_name.split(',')[0];
            const state = addr.state || addr.region || addr.country || '';
            this.input.value = state ? `${city}, ${state}` : city;
        }
        
        this.suggestions.classList.remove('show');
    }
    
    handleKeydown(e) {
        const items = this.suggestions.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.activeIndex = Math.min(this.activeIndex + 1, items.length - 1);
            this.updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.activeIndex = Math.max(this.activeIndex - 1, 0);
            this.updateActiveItem(items);
        } else if (e.key === 'Enter' && this.activeIndex >= 0) {
            e.preventDefault();
            this.selectItem(this.activeIndex);
        } else if (e.key === 'Escape') {
            this.suggestions.classList.remove('show');
        }
    }
    
    updateActiveItem(items) {
        items.forEach((item, index) => {
            item.classList.toggle('active', index === this.activeIndex);
        });
        
        // Scroll into view
        if (this.activeIndex >= 0 && items[this.activeIndex]) {
            items[this.activeIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize autocompletes
document.addEventListener('DOMContentLoaded', () => {
    new Autocomplete('home_gym', 'gym-suggestions', 'gym');
    new Autocomplete('city', 'city-suggestions', 'city');
    
    // Gender box toggle
    document.querySelectorAll('.gender-box').forEach(box => {
        box.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected', checkbox.checked);
        });
    });
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
