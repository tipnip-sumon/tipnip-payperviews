# 📖 How Users Can View Markdown Files

## 🌐 **User Access URLs**

### **Main Documentation Center**
- **URL**: `http://your-domain.com/docs`
- **Description**: Main hub with all categories and search functionality

### **Category-Based Viewing**
- **URL Pattern**: `http://your-domain.com/docs/{category}`
- **Examples**:
  - `http://your-domain.com/docs/tutorial`
  - `http://your-domain.com/docs/help`
  - `http://your-domain.com/docs/faq`

### **Individual Document Viewing**
- **URL Pattern**: `http://your-domain.com/docs/{category}/{slug}`
- **Examples**:
  - `http://your-domain.com/docs/tutorial/how-to-invest-in-payperviews`
  - `http://your-domain.com/docs/help/understanding-kyc-verification`
  - `http://your-domain.com/docs/guide/deposit-and-withdrawal-guide`

### **Quick Access Links**
- **Privacy Policy**: `http://your-domain.com/privacy-policy`
- **Terms & Conditions**: `http://your-domain.com/terms-and-conditions`
- **FAQ**: `http://your-domain.com/faq`
- **Help Center**: `http://your-domain.com/help`

## 🔍 **Available Features for Users**

### **1. Search Functionality**
- Real-time search across all documents
- Search by title, content, and meta description
- Instant results with excerpts

### **2. Category Browsing**
- Documents organized by categories
- Category descriptions and icons
- Document count per category

### **3. Document Reading Experience**
- Clean, readable markdown rendering
- Table of contents for long documents
- Reading time estimation
- View counter
- Related document suggestions

### **4. Interactive Features**
- Print document functionality
- Copy link to clipboard
- Smooth scrolling table of contents
- Responsive design for mobile/desktop

### **5. Search API Endpoints**
- `/api/docs/search?q=query` - Search documents
- `/api/docs/list` - Get document list
- `/api/docs/{id}/view` - Record document view

## 📊 **Current Available Documents**

The system currently has these documents ready for viewing:

1. **Welcome to PayPerViews** (`/docs/documentation/welcome-to-payperviews`)
2. **How to Invest in PayPerViews** (`/docs/tutorial/how-to-invest-in-payperviews`)
3. **Understanding KYC Verification** (`/docs/help/understanding-kyc-verification`)
4. **Deposit and Withdrawal Guide** (`/docs/guide/deposit-and-withdrawal-guide`)
5. **Privacy Policy** (`/docs/policy/privacy-policy` or `/privacy-policy`)
6. **Terms and Conditions** (`/docs/terms/terms-and-conditions` or `/terms-and-conditions`)
7. **Frequently Asked Questions** (`/docs/faq/frequently-asked-questions` or `/faq`)

## 🎯 **Access Examples**

### **For End Users:**
```
1. Visit: http://your-domain.com/docs
   - Browse all categories
   - Use search to find specific topics
   - View popular and recent documents

2. Direct access: http://your-domain.com/faq
   - Instantly view FAQ page

3. Category browsing: http://your-domain.com/docs/tutorial
   - See all tutorial documents
```

### **For Developers (API):**
```javascript
// Search documents
fetch('/api/docs/search?q=investment')
  .then(response => response.json())
  .then(data => console.log(data.results));

// Get document list
fetch('/api/docs/list?category=tutorial')
  .then(response => response.json())
  .then(data => console.log(data.documents));
```

## 🛠 **Integration with Existing Layout**

The markdown viewer uses your existing user layout (`layouts.user`) and integrates seamlessly with:
- Navigation menus
- User authentication
- Responsive design
- Existing styling

## 📱 **Mobile-Friendly Features**

- Responsive design
- Touch-friendly navigation
- Collapsible table of contents
- Optimized reading experience
- Mobile search interface

## 🔗 **Adding to Navigation Menu**

You can add these links to your main navigation:

```html
<li><a href="{{ route('docs.index') }}">Help Center</a></li>
<li><a href="{{ route('faq') }}">FAQ</a></li>
<li><a href="{{ route('privacy-policy') }}">Privacy</a></li>
<li><a href="{{ route('terms-and-conditions') }}">Terms</a></li>
```

## 🎨 **Customization**

The views are fully customizable:
- Modify styles in the view files
- Change layout structure
- Add custom JavaScript functionality
- Integrate with your design system

---

**✅ The markdown system is now fully operational and ready for user access!**
