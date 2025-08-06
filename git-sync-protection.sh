#!/bin/bash
# Git Synchronization Protection System
# This script helps prevent and resolve Git synchronization issues

echo "🔧 Git Synchronization Problem Prevention System"
echo "=================================================="

# Function to configure Git settings
configure_git() {
    echo "📝 Configuring Git settings to prevent sync issues..."
    
    # Core configurations
    git config core.autocrlf false
    git config core.eol lf
    git config core.safecrlf warn
    git config core.filemode false
    git config pull.rebase false
    git config push.default simple
    git config merge.ours.driver "true"
    
    # Better conflict resolution
    git config merge.tool vscode
    git config diff.tool vscode
    
    echo "✅ Git configuration completed"
}

# Function to create backup of critical files
backup_critical_files() {
    echo "💾 Creating backup of critical files..."
    
    BACKUP_DIR=".git/backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    
    CRITICAL_FILES=(
        "app/Http/Controllers/Auth/RegisterController.php"
        "app/Models/User.php"
        "public/assets/js/session-manager.js"
        "resources/views/auth/register.blade.php"
        "resources/views/components/smart_layout.blade.php"
        "routes/web.php"
    )
    
    for file in "${CRITICAL_FILES[@]}"; do
        if [ -f "$file" ]; then
            cp "$file" "$BACKUP_DIR/"
            echo "✅ Backed up: $file"
        fi
    done
    
    echo "📁 Backup created at: $BACKUP_DIR"
}

# Function to check for real merge conflicts (not false positives)
check_real_conflicts() {
    echo "🔍 Checking for actual merge conflicts..."
    
    # Look for actual merge conflict markers in PHP, JS, and Blade files only
    # Exclude vendor, node_modules, and asset libraries
    CONFLICT_FILES=$(find . -type f \( -name "*.php" -o -name "*.js" -o -name "*.blade.php" -o -name "*.json" \) \
        ! -path "./vendor/*" \
        ! -path "./node_modules/*" \
        ! -path "./public/assets/libs/*" \
        ! -path "./public/assets - Copy/*" \
        -exec grep -l "^<<<<<<< HEAD\|^=======$\|^>>>>>>> " {} \; 2>/dev/null)
    
    if [ -n "$CONFLICT_FILES" ]; then
        echo "❌ ERROR: Real merge conflicts found in:"
        echo "$CONFLICT_FILES"
        return 1
    else
        echo "✅ No merge conflicts detected"
        return 0
    fi
}

# Function to sync safely
safe_sync() {
    echo "🔄 Performing safe Git synchronization..."
    
    # Stash current changes
    git stash push -m "Auto-stash before sync $(date)"
    
    # Pull latest changes
    git pull origin mainur_sir --no-rebase
    
    # Pop stashed changes
    git stash pop
    
    echo "✅ Safe sync completed"
}

# Function to commit and push safely
safe_commit_push() {
    echo "💾 Performing safe commit and push..."
    
    # Add all changes
    git add .
    
    # Commit with timestamp
    git commit -m "Sync update: $(date '+%Y-%m-%d %H:%M:%S') - Prevent synchronization issues"
    
    # Push to remote
    git push origin mainur_sir
    
    echo "✅ Safe commit and push completed"
}

# Function to show Git status
show_status() {
    echo "📊 Current Git Status:"
    echo "======================"
    git status --short
    echo ""
    echo "📈 Recent commits:"
    git log --oneline -5
}

# Main menu
show_menu() {
    echo ""
    echo "🛠️  Git Sync Protection Menu:"
    echo "1. Configure Git settings"
    echo "2. Backup critical files"
    echo "3. Check for conflicts"
    echo "4. Safe sync (pull)"
    echo "5. Safe commit & push"
    echo "6. Show status"
    echo "7. Full protection setup"
    echo "8. Exit"
    echo ""
    read -p "Choose an option (1-8): " choice
}

# Main execution
main() {
    while true; do
        show_menu
        
        case $choice in
            1)
                configure_git
                ;;
            2)
                backup_critical_files
                ;;
            3)
                check_real_conflicts
                ;;
            4)
                safe_sync
                ;;
            5)
                if check_real_conflicts; then
                    safe_commit_push
                else
                    echo "❌ Cannot commit with conflicts. Please resolve them first."
                fi
                ;;
            6)
                show_status
                ;;
            7)
                echo "🚀 Running full protection setup..."
                configure_git
                backup_critical_files
                check_real_conflicts
                echo "✅ Full protection setup completed!"
                ;;
            8)
                echo "👋 Goodbye!"
                exit 0
                ;;
            *)
                echo "❌ Invalid option. Please choose 1-8."
                ;;
        esac
        
        echo ""
        read -p "Press Enter to continue..."
    done
}

# Run the main function
main
