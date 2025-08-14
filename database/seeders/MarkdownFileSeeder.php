<?php

namespace Database\Seeders;

use App\Models\MarkdownFile;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MarkdownFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : null;

        // Create demo markdown files
        $markdownFiles = [
            [
                'title' => 'Welcome to  PayPerViews',
                'slug' => 'welcome-to-payperviews',
                'content' => $this->getWelcomeContent(),
                'meta_description' => 'Welcome guide for new users of  PayPerViews platform',
                'meta_keywords' => 'welcome, guide, payperviews, getting started',
                'category' => 'documentation',
                'tags' => ['welcome', 'guide', 'getting-started'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'How to Invest in PayPerViews',
                'slug' => 'how-to-invest-in-payperviews',
                'content' => $this->getInvestmentGuideContent(),
                'meta_description' => 'Complete guide on how to make investments in PayPerViews',
                'meta_keywords' => 'investment, guide, payperviews, earning, profits',
                'category' => 'tutorial',
                'tags' => ['investment', 'tutorial', 'earning'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Understanding KYC Verification',
                'slug' => 'understanding-kyc-verification',
                'content' => $this->getKycGuideContent(),
                'meta_description' => 'Learn about KYC verification process and requirements',
                'meta_keywords' => 'kyc, verification, identity, documents, compliance',
                'category' => 'help',
                'tags' => ['kyc', 'verification', 'compliance'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Deposit and Withdrawal Guide',
                'slug' => 'deposit-and-withdrawal-guide',
                'content' => $this->getPaymentGuideContent(),
                'meta_description' => 'Complete guide for deposits and withdrawals on PayPerViews platform',
                'meta_keywords' => 'deposit, withdrawal, payment, cryptocurrency, fiat',
                'category' => 'guide',
                'tags' => ['deposit', 'withdrawal', 'payment'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => $this->getPrivacyPolicyContent(),
                'meta_description' => 'Privacy policy for PayPerViews platform',
                'meta_keywords' => 'privacy, policy, data protection, gdpr, compliance',
                'category' => 'policy',
                'tags' => ['privacy', 'policy', 'legal'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'content' => $this->getTermsContent(),
                'meta_description' => 'Terms and conditions for using PayPerViews',
                'meta_keywords' => 'terms, conditions, agreement, legal, usage',
                'category' => 'terms',
                'tags' => ['terms', 'conditions', 'legal'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Frequently Asked Questions',
                'slug' => 'frequently-asked-questions',
                'content' => $this->getFaqContent(),
                'meta_description' => 'Common questions and answers about PayPerViews',
                'meta_keywords' => 'faq, questions, answers, help, support',
                'category' => 'faq',
                'tags' => ['faq', 'help', 'support'],
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $adminId,
                'created_by' => $adminId,
            ],
            [
                'title' => 'API Documentation',
                'slug' => 'api-documentation',
                'content' => $this->getApiDocumentationContent(),
                'meta_description' => 'API documentation for PayPerViews platform',
                'meta_keywords' => 'api, documentation, integration, endpoints, development',
                'category' => 'api',
                'tags' => ['api', 'documentation', 'development'],
                'status' => 'draft',
                'is_published' => false,
                'author_id' => $adminId,
                'created_by' => $adminId,
            ]
        ];

        foreach ($markdownFiles as $fileData) {
            $markdownFile = MarkdownFile::create($fileData);
            
            // Save to physical file
            $markdownFile->saveToFile();
        }

        $this->command->info('Demo markdown files seeded successfully!');
    }

    private function getWelcomeContent()
    {
        return "# Welcome to PayPerViews

## Getting Started

Welcome to **PayPerViews**, the premier platform for earning through video viewing and investment opportunities.

### What is PayPerViews?

PayPerViews is an innovative platform that allows users to:

- **Earn by Viewing**: Watch videos and earn rewards
- **Invest Smartly**: Make strategic investments with attractive returns
- **Grow Your Portfolio**: Build and manage your investment portfolio
- **Withdraw Easily**: Simple and secure withdrawal process

### Quick Start Guide

1. **Complete Registration**: Fill out your profile information
2. **Verify Your Account**: Complete KYC verification process
3. **Make Your First Deposit**: Add funds to start investing
4. **Start Earning**: Begin viewing content and making investments
5. **Track Progress**: Monitor your earnings and portfolio growth

### Key Features

- ✅ **Secure Platform**: Advanced security measures
- ✅ **Multiple Payment Methods**: Support for various payment options
- ✅ **Real-time Tracking**: Live updates on your investments
- ✅ **24/7 Support**: Round-the-clock customer assistance
- ✅ **Mobile Friendly**: Access from any device

### Need Help?

If you have any questions, please:

- Check our [FAQ section](/faq)
- Contact our support team
- Join our community forums

**Happy earning!** 🚀";
    }

    private function getInvestmentGuideContent()
    {
        return "# How to Invest in PayPerViews

## Investment Overview

Learn how to make profitable investments on the PayPerViews platform.

### Investment Process

#### Step 1: Account Preparation
- Complete KYC verification
- Add funds to your wallet
- Review available investment plans

#### Step 2: Choose Your Investment Plan

We offer several investment options:

| Plan | Minimum | Maximum | Return Rate | Duration |
|------|---------|---------|-------------|----------|
| Starter | \$10 | \$99 | 5% daily | 30 days |
| Bronze | \$100 | \$499 | 7% daily | 45 days |
| Silver | \$500 | \$999 | 10% daily | 60 days |
| Gold | \$1000 | \$4999 | 12% daily | 90 days |
| Platinum | \$5000+ | Unlimited | 15% daily | 120 days |

#### Step 3: Make Your Investment
1. Select your preferred plan
2. Enter investment amount
3. Confirm transaction
4. Track your investment progress

### Investment Tips

> **Pro Tip**: Start with smaller amounts to understand the platform before making larger investments.

#### Risk Management
- Diversify your investments
- Start with minimum amounts
- Monitor market conditions
- Reinvest profits wisely

#### Maximizing Returns
- Take advantage of compound interest
- Participate in bonus programs
- Refer friends for additional rewards
- Stay updated with platform announcements

### Withdrawal Process

Earnings can be withdrawn:
- **Minimum Withdrawal**: \$5
- **Processing Time**: 24-48 hours
- **Supported Methods**: Bank transfer, cryptocurrency, e-wallets

### Important Notes

⚠️ **Investment Risks**: All investments carry risk. Never invest more than you can afford to lose.

📈 **Performance Tracking**: Use our dashboard to monitor your investment performance in real-time.

🔒 **Security**: Your investments are protected by advanced security measures.";
    }

    private function getKycGuideContent()
    {
        return "# Understanding KYC Verification

## What is KYC?

**Know Your Customer (KYC)** is a verification process required by financial regulations to verify the identity of our users.

### Why KYC is Required

- **Legal Compliance**: Meet international financial regulations
- **Security**: Protect your account from unauthorized access
- **Trust**: Build a secure and trustworthy platform
- **Anti-Money Laundering**: Prevent fraudulent activities

### KYC Verification Process

#### Step 1: Personal Information
Provide accurate personal details:
- Full legal name
- Date of birth
- Address
- Phone number
- Email address

#### Step 2: Document Upload
Submit clear photos of:

**Identity Documents** (Choose one):
- Passport
- National ID card
- Driver's license

**Address Proof** (Choose one):
- Utility bill (recent)
- Bank statement
- Government correspondence

#### Step 3: Selfie Verification
- Take a clear selfie
- Hold your ID document next to your face
- Ensure good lighting and visibility

### Document Requirements

#### Photo Quality Guidelines
- **High Resolution**: Clear and readable text
- **Good Lighting**: Avoid shadows and glare
- **Full Document**: All corners visible
- **No Editing**: Original, unmodified images

#### Common Rejection Reasons
- Blurry or low-quality images
- Expired documents
- Partial document visibility
- Name mismatch between documents

### Verification Timeline

| Verification Level | Processing Time | Features Unlocked |
|-------------------|-----------------|-------------------|
| Level 1 | 24 hours | Basic features |
| Level 2 | 48-72 hours | Full investment access |
| Level 3 | 5-7 days | Premium features |

### After Verification

Once verified, you'll enjoy:
- ✅ Full platform access
- ✅ Higher deposit/withdrawal limits
- ✅ Priority customer support
- ✅ Access to premium investment plans

### Privacy and Security

Your personal information is:
- **Encrypted**: Protected with advanced encryption
- **Confidential**: Never shared with third parties
- **Secure**: Stored in secure data centers
- **Compliant**: Handled according to GDPR standards

### Need Help?

If you encounter issues:
1. Check document requirements
2. Ensure photo quality
3. Contact our support team
4. Use live chat for immediate assistance

**Remember**: KYC is a one-time process that ensures the security of your account and compliance with regulations.";
    }

    private function getPaymentGuideContent()
    {
        return "# Deposit and Withdrawal Guide

## Payment Methods Overview

PayPerViews supports multiple payment methods for your convenience.

### Supported Payment Methods

#### Cryptocurrency
- **Bitcoin (BTC)**
- **Ethereum (ETH)**
- **Litecoin (LTC)**
- **USDT (Tether)**
- **And 15+ other cryptocurrencies**

#### Fiat Currency
- **Stripe** - Credit/Debit cards
- **PayPal** - Digital wallet
- **Bank Transfer** - Direct bank transfers
- **Skrill** - E-wallet service
- **Perfect Money** - Digital payment system

### Making a Deposit

#### Step 1: Access Deposit Page
1. Login to your account
2. Navigate to **Wallet** → **Deposit**
3. Select your preferred payment method

#### Step 2: Enter Amount
- Minimum deposit: \$10
- Maximum deposit: \$50,000
- Choose amount within limits

#### Step 3: Complete Payment
- Follow payment gateway instructions
- Verify transaction details
- Complete payment process

#### Step 4: Confirmation
- Receive confirmation email
- Funds appear in your wallet
- Ready to invest or withdraw

### Withdrawal Process

#### Withdrawal Requirements
- **Minimum Amount**: \$5
- **KYC Verification**: Required
- **Processing Time**: 24-48 hours

#### How to Withdraw

1. **Go to Withdrawal Section**
   - Navigate to **Wallet** → **Withdraw**

2. **Select Payment Method**
   - Choose from available options
   - Ensure method supports withdrawals

3. **Enter Details**
   - Withdrawal amount
   - Payment details (wallet address, account info)
   - Verification code

4. **Confirm Request**
   - Review all details
   - Submit withdrawal request
   - Await processing

### Transaction Fees

| Payment Method | Deposit Fee | Withdrawal Fee |
|---------------|-------------|----------------|
| Bitcoin | 0% | 0.001 BTC |
| Ethereum | 0% | 0.01 ETH |
| USDT | 0% | 5 USDT |
| Stripe | 2.9% | 2.9% |
| PayPal | 3.5% | 3.5% |
| Bank Transfer | 1% | 1% |

### Processing Times

#### Deposits
- **Cryptocurrency**: 1-3 confirmations
- **Credit Card**: Instant
- **Bank Transfer**: 1-3 business days
- **E-wallets**: Instant

#### Withdrawals
- **Cryptocurrency**: 24 hours
- **Bank Transfer**: 3-5 business days
- **E-wallets**: 24-48 hours

### Security Measures

#### For Your Protection
- **2FA Authentication**: Enable for extra security
- **Email Verification**: Required for all transactions
- **SSL Encryption**: All data is encrypted
- **Cold Storage**: Cryptocurrency funds secured offline

#### Best Practices
- Use secure networks for transactions
- Keep your login credentials safe
- Enable all security features
- Report suspicious activity immediately

### Common Issues and Solutions

#### Deposit Not Received
1. Check transaction status
2. Verify payment method
3. Contact support with transaction ID
4. Allow processing time

#### Withdrawal Delayed
1. Ensure KYC verification is complete
2. Check minimum withdrawal amount
3. Verify payment details
4. Contact support if delayed beyond normal time

### Support

For payment-related issues:
- **Live Chat**: Available 24/7
- **Email Support**: support@payperviews.net
- **Help Desk**: Submit a ticket
- **Phone Support**: Available during business hours

**Remember**: Always double-check payment details before confirming transactions.";
    }

    private function getPrivacyPolicyContent()
    {
        return "# Privacy Policy

*Last updated: " . date('F j, Y') . "*

## Introduction

PayPerViews (\"we,\" \"our,\" or \"us\") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information.

## Information We Collect

### Personal Information
- **Identity Information**: Name, date of birth, nationality
- **Contact Information**: Email address, phone number, postal address
- **Financial Information**: Bank account details, payment information
- **Verification Documents**: ID documents, proof of address

### Usage Information
- **Device Information**: IP address, browser type, operating system
- **Activity Data**: Login times, pages visited, transactions
- **Cookies**: Website preferences and analytics

## How We Use Your Information

### Primary Uses
- **Account Management**: Create and maintain your account
- **Service Provision**: Provide our investment services
- **Transaction Processing**: Handle deposits and withdrawals
- **Customer Support**: Respond to inquiries and provide assistance

### Secondary Uses
- **Security**: Prevent fraud and ensure platform security
- **Compliance**: Meet legal and regulatory requirements
- **Improvement**: Enhance our services and user experience
- **Communication**: Send updates and promotional materials

## Information Sharing

### We May Share Information With:
- **Service Providers**: Payment processors, KYC verification services
- **Legal Authorities**: When required by law or regulation
- **Business Partners**: For legitimate business purposes
- **Professional Advisors**: Lawyers, accountants, auditors

### We Never Share:
- Personal information for marketing without consent
- Financial details with unauthorized parties
- Account passwords or security information

## Data Security

### Security Measures
- **Encryption**: All data transmitted using SSL/TLS encryption
- **Access Controls**: Limited access to authorized personnel only
- **Regular Audits**: Security assessments and vulnerability testing
- **Secure Storage**: Data stored in secure, monitored facilities

### Your Responsibilities
- Keep login credentials secure
- Use strong, unique passwords
- Enable two-factor authentication
- Report suspicious activity immediately

## Your Rights

### Data Protection Rights
- **Access**: Request a copy of your personal data
- **Correction**: Update incorrect or incomplete information
- **Deletion**: Request deletion of your personal data
- **Portability**: Receive your data in a portable format
- **Objection**: Object to certain uses of your information

### How to Exercise Rights
Contact our Data Protection Officer:
- Email: privacy@payperviews.net
- Subject: Data Protection Request
- Include: Your full name and account details

## Cookies and Tracking

### Types of Cookies
- **Essential**: Required for basic site functionality
- **Analytics**: Help us understand site usage
- **Preferences**: Remember your settings
- **Marketing**: Deliver relevant advertisements

### Cookie Management
You can control cookies through your browser settings. Note that disabling cookies may affect site functionality.

## International Transfers

Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place for such transfers.

## Data Retention

We retain your information for as long as necessary to:
- Provide our services
- Comply with legal obligations
- Resolve disputes
- Enforce our agreements

## Children's Privacy

Our services are not intended for individuals under 18. We do not knowingly collect information from minors.

## Changes to Privacy Policy

We may update this policy periodically. We'll notify you of significant changes via:
- Email notification
- Website announcement
- In-app notification

## Contact Information

### Data Protection Officer
- **Email**: privacy@payperviews.net
- **Address**: [Company Address]
- **Phone**: [Support Phone Number]

### General Support
- **Email**: support@payperviews.net
- **Live Chat**: Available 24/7 on our website
- **Help Center**: [Help Center URL]

## Compliance

This Privacy Policy complies with:
- General Data Protection Regulation (GDPR)
- California Consumer Privacy Act (CCPA)
- Other applicable data protection laws

By using our services, you acknowledge that you have read and understood this Privacy Policy.";
    }

    private function getTermsContent()
    {
        return "# Terms and Conditions

*Last updated: " . date('F j, Y') . "*

## Acceptance of Terms

By accessing and using PayPerViews (\"the Platform\"), you agree to be bound by these Terms and Conditions.

## Definitions

- **\"Platform\"**: PayPerViews website and services
- **\"User\"**: Any person using our services
- **\"We/Us/Our\"**: PayPerViews company
- **\"Account\"**: Your registered user account
- **\"Services\"**: All services provided by the Platform

## Eligibility

### Requirements
- Must be at least 18 years old
- Must have legal capacity to enter contracts
- Must not be restricted by applicable laws
- Must provide accurate registration information

### Prohibited Users
- Residents of restricted jurisdictions
- Individuals on sanctions lists
- Those with previous account violations
- Minors under 18 years of age

## Account Registration

### Registration Process
1. Provide accurate personal information
2. Complete email verification
3. Complete KYC verification
4. Accept these Terms and Conditions

### Account Responsibilities
- Maintain account security
- Keep information current and accurate
- Notify us of unauthorized access
- Comply with all platform rules

## Investment Services

### Service Description
- Investment opportunity platform
- Video viewing rewards system
- Portfolio management tools
- Withdrawal and deposit services

### Investment Risks
- All investments carry risk of loss
- Past performance doesn't guarantee future results
- Market conditions may affect returns
- Platform is not liable for investment losses

## User Obligations

### Prohibited Activities
- Creating multiple accounts
- Using automated systems or bots
- Attempting to manipulate the platform
- Engaging in fraudulent activities
- Violating applicable laws

### Compliance Requirements
- Complete KYC verification when requested
- Provide accurate information
- Pay applicable taxes
- Follow all platform guidelines

## Financial Terms

### Deposits and Withdrawals
- Minimum deposit: \$10
- Minimum withdrawal: \$5
- Processing times as specified
- Fees as disclosed on the platform

### Investment Returns
- Returns are subject to market conditions
- No guarantee of profits
- Historical performance not indicative of future results
- Platform reserves right to modify plans

## Intellectual Property

### Our Rights
- Platform content and design are our property
- Trademarks and logos are protected
- Users may not copy or reproduce content
- All rights reserved unless explicitly granted

### User Content
- Users retain rights to their content
- Grant us license to use submitted content
- Must not infringe third-party rights
- Must comply with content guidelines

## Privacy and Data Protection

Your privacy is important to us. Our Privacy Policy explains how we collect, use, and protect your information.

## Platform Availability

### Service Levels
- We strive for 99.9% uptime
- Maintenance windows may be scheduled
- No guarantee of uninterrupted service
- Users notified of significant outages

### Service Modifications
We reserve the right to:
- Modify or discontinue services
- Update platform features
- Change terms with notice
- Suspend accounts for violations

## Limitation of Liability

### Disclaimer
- Services provided \"as is\"
- No warranties, express or implied
- Platform not liable for user losses
- Maximum liability limited to account balance

### Excluded Damages
We are not liable for:
- Indirect or consequential damages
- Loss of profits or data
- Business interruption
- Force majeure events

## Indemnification

Users agree to indemnify and hold harmless the Platform from claims arising from:
- User's use of the platform
- Violation of these terms
- Infringement of third-party rights
- Fraudulent or illegal activities

## Dispute Resolution

### Governing Law
These terms are governed by [Applicable Jurisdiction] law.

### Dispute Process
1. Contact customer support first
2. Attempt good faith resolution
3. Binding arbitration if unresolved
4. No class action lawsuits

## Termination

### Termination Rights
- Users may close accounts at any time
- We may suspend/terminate for violations
- Outstanding balances must be settled
- Some provisions survive termination

### Effect of Termination
- Access to platform ends
- Data may be retained as required
- Outstanding obligations remain
- User rights under these terms end

## Amendments

### Changes to Terms
- Terms may be updated periodically
- Users notified of material changes
- Continued use constitutes acceptance
- Previous versions superseded

## Severability

If any provision is found unenforceable, the remainder of these terms remain in effect.

## Contact Information

For questions about these Terms:
- **Email**: legal@payperviews.net
- **Address**: [Company Address]
- **Phone**: [Support Phone Number]

## Acknowledgment

By using our platform, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.";
    }

    private function getFaqContent()
    {
        return "# Frequently Asked Questions

## General Questions

### What is PayPerViews?
PayPerViews is an investment platform that allows users to earn money through video viewing and strategic investments.

### How does the platform work?
Users can deposit funds, choose investment plans, watch videos for additional rewards, and withdraw their earnings.

### Is PayPerViews legitimate?
Yes, we are a registered and regulated financial services provider committed to transparency and user security.

## Account and Registration

### How do I create an account?
1. Visit our registration page
2. Fill in your personal information
3. Verify your email address
4. Complete KYC verification

### What documents do I need for verification?
- Government-issued ID (passport, driver's license, or national ID)
- Proof of address (utility bill or bank statement)
- Clear selfie with your ID document

### How long does verification take?
- Level 1: 24 hours
- Level 2: 48-72 hours
- Level 3: 5-7 business days

### Can I have multiple accounts?
No, each user is allowed only one account. Multiple accounts will result in suspension.

## Investments

### What investment plans are available?
We offer five investment tiers:
- **Starter**: \$10-\$99, 5% daily for 30 days
- **Bronze**: \$100-\$499, 7% daily for 45 days
- **Silver**: \$500-\$999, 10% daily for 60 days
- **Gold**: \$1000-\$4999, 12% daily for 90 days
- **Platinum**: \$5000+, 15% daily for 120 days

### What's the minimum investment?
The minimum investment is \$10 for the Starter plan.

### Are there any guarantees?
While we strive for consistent returns, all investments carry risk. Past performance doesn't guarantee future results.

### Can I reinvest my earnings?
Yes, you can reinvest your earnings to compound your returns.

## Deposits and Withdrawals

### What payment methods do you accept?
- Cryptocurrencies (Bitcoin, Ethereum, USDT, etc.)
- Credit/Debit cards (via Stripe)
- PayPal
- Bank transfers
- E-wallets (Skrill, Perfect Money)

### What's the minimum deposit?
The minimum deposit is \$10.

### What's the minimum withdrawal?
The minimum withdrawal is \$5.

### How long do withdrawals take?
- Cryptocurrency: 24 hours
- Bank transfers: 3-5 business days
- E-wallets: 24-48 hours

### Are there any fees?
Fees vary by payment method. Check our fee schedule for details.

## Security

### How secure is my account?
We use industry-standard security measures including:
- SSL encryption
- Two-factor authentication
- Cold storage for cryptocurrencies
- Regular security audits

### What should I do if I suspect unauthorized access?
1. Change your password immediately
2. Enable 2FA if not already active
3. Contact our support team
4. Review your account activity

### How is my personal information protected?
We follow strict data protection protocols and comply with GDPR and other privacy regulations.

## Technical Issues

### I can't log into my account. What should I do?
1. Check your email and password
2. Try resetting your password
3. Clear your browser cache
4. Contact support if issues persist

### My deposit isn't showing up. Why?
- Check if the transaction was completed
- Allow processing time for your payment method
- Verify you sent to the correct address/details
- Contact support with transaction details

### The website is slow or not loading. What's wrong?
- Check your internet connection
- Try a different browser
- Clear your browser cache
- Check our status page for known issues

## Support

### How can I contact customer support?
- **Live Chat**: Available 24/7 on our website
- **Email**: support@payperviews.net
- **Help Desk**: Submit a ticket through your account
- **Phone**: Available during business hours

### What information should I include when contacting support?
- Your account email
- Description of the issue
- Screenshots if applicable
- Transaction IDs for payment issues
- Any error messages you received

### How quickly will I receive a response?
- Live chat: Immediate response
- Email: Within 24 hours
- Help desk tickets: Within 48 hours
- Phone: Immediate during business hours

## Legal and Compliance

### Is PayPerViews regulated?
Yes, we operate under applicable financial regulations and maintain necessary licenses.

### What countries are restricted?
Some countries may be restricted due to local regulations. Check our terms of service for the current list.

### Do I need to pay taxes on my earnings?
Tax obligations vary by jurisdiction. Consult with a tax professional in your country.

## Additional Questions

### Can I refer friends?
Yes, we have a referral program that rewards you for bringing new users to the platform.

### Is there a mobile app?
Yes, we have mobile apps available for iOS and Android devices.

### How often are returns paid?
Returns are typically paid daily, but this may vary by investment plan.

### Can I cancel my investment?
Investment terms vary by plan. Check your specific plan details for cancellation policies.

---

**Still have questions?** Contact our support team - we're here to help!";
    }

    private function getApiDocumentationContent()
    {
        return "# API Documentation

## Overview

The PayPerViews API provides programmatic access to platform features and data.

### Base URL
```
https://api.payperviews.net/v1
```

### Authentication
All API requests require authentication using API keys.

```http
Authorization: Bearer YOUR_API_KEY
```

## Getting Started

### API Key Generation
1. Login to your account
2. Navigate to Settings → API Keys
3. Generate a new API key
4. Store securely (not recoverable)

### Rate Limits
- **Standard**: 1000 requests/hour
- **Premium**: 5000 requests/hour
- **Enterprise**: Unlimited

## Endpoints

### Authentication

#### POST /auth/token
Generate access token

**Request:**
```json
{
  \"api_key\": \"your_api_key\",
  \"secret\": \"your_secret\"
}
```

**Response:**
```json
{
  \"access_token\": \"jwt_token\",
  \"expires_in\": 3600,
  \"token_type\": \"Bearer\"
}
```

### Account Information

#### GET /account
Get account details

**Response:**
```json
{
  \"id\": 12345,
  \"email\": \"user@example.com\",
  \"balance\": 1000.50,
  \"status\": \"active\",
  \"created_at\": \"2025-01-01T00:00:00Z\"
}
```

### Investments

#### GET /investments
List user investments

**Parameters:**
- `status` (optional): active, completed, cancelled
- `limit` (optional): 1-100, default 20
- `offset` (optional): default 0

**Response:**
```json
{
  \"investments\": [
    {
      \"id\": 123,
      \"amount\": 100.00,
      \"plan\": \"bronze\",
      \"status\": \"active\",
      \"created_at\": \"2025-01-01T00:00:00Z\"
    }
  ],
  \"total\": 5,
  \"limit\": 20,
  \"offset\": 0
}
```

#### POST /investments
Create new investment

**Request:**
```json
{
  \"plan_id\": 2,
  \"amount\": 100.00
}
```

### Transactions

#### GET /transactions
List account transactions

**Parameters:**
- `type` (optional): deposit, withdrawal, earning
- `status` (optional): pending, completed, failed
- `from_date` (optional): YYYY-MM-DD
- `to_date` (optional): YYYY-MM-DD

**Response:**
```json
{
  \"transactions\": [
    {
      \"id\": 456,
      \"type\": \"deposit\",
      \"amount\": 100.00,
      \"status\": \"completed\",
      \"created_at\": \"2025-01-01T00:00:00Z\"
    }
  ]
}
```

## Error Handling

### Error Codes
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `429` - Rate Limit Exceeded
- `500` - Internal Server Error

### Error Response Format
```json
{
  \"error\": {
    \"code\": \"INVALID_REQUEST\",
    \"message\": \"The request is invalid\",
    \"details\": \"Specific error details\"
  }
}
```

## SDKs and Libraries

### Official SDKs
- **JavaScript/Node.js**: npm install payperviews-sdk
- **Python**: pip install payperviews-python
- **PHP**: composer require payperviews/php-sdk

### Community Libraries
- **Ruby**: payperviews-ruby gem
- **Go**: github.com/payperviews/go-sdk
- **Java**: Maven payperviews-java

## Webhooks

### Webhook Events
- `investment.created`
- `investment.completed`
- `transaction.completed`
- `account.updated`

### Webhook Configuration
Configure webhooks in your account settings:
1. Add webhook URL
2. Select events to receive
3. Configure secret for verification

### Webhook Payload
```json
{
  \"event\": \"investment.completed\",
  \"data\": {
    \"investment_id\": 123,
    \"amount\": 100.00,
    \"profit\": 15.00
  },
  \"timestamp\": \"2025-01-01T00:00:00Z\"
}
```

## Testing

### Sandbox Environment
```
https://sandbox-api.payperviews.net/v1
```

### Test API Keys
Use sandbox API keys for testing. No real money is involved.

## Support

### Developer Support
- **Email**: developers@payperviews.net
- **Discord**: [Developer Community]
- **Documentation**: docs.payperviews.net
- **Status Page**: status.payperviews.net

### Response Times
- **Critical Issues**: Within 2 hours
- **General Questions**: Within 24 hours
- **Feature Requests**: Within 1 week

---

**Note**: This API documentation is currently in draft status and subject to change.";
    }
}
