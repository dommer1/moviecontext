# Technical Specification: AI-Powered Czech Film Blog Platform

## Project Overview
Automated Czech film blog that generates original content from multiple sources using AI agents with distinct writing personalities. The platform operates autonomously - no admin panel needed. Everything is generated automatically through AI including content extraction, article writing, and publishing.

## Tech Stack
- **Backend**: Laravel (latest stable)
- **Admin Panel**: FilamentPHP 4
- **Frontend**: TailwindCSS, Livewire, Alpine.js
- **Database**: MySQL/PostgreSQL
- **Queue System**: Laravel Queue (Redis recommended)
- **Scheduling**: Laravel Task Scheduler
- **AI Integration**: Prismphp
  - OpenAI API / Anthropic Claude API
  - Cheap model for extraction: GPT-3.5-turbo or Claude Haiku
  - Premium model for content generation: GPT-4-turbo or Claude Sonnet

## Design Reference
Visual design should be inspired by: https://magazinkontext.sk/
- Modern, clean layout
- Strong imagery
- Clear typography
- Responsive grid system

---

## Core Functionality

### 1. Content Sources System

**Database Structure:**
- Store list of source websites (URLs)
- Each source has: name, URL, type (news/review/streaming), language (cs/en/sk), active status
- Track when each source was last checked
- **NO CSS selectors needed** - AI extracts content automatically

**Key Point:** Just add URL to database, AI handles everything else. No manual configuration per website.

### 2. AI Author Personas

Create 3 fictional Czech authors with distinct personalities:

**Author 1: Professional Film Critic**
- Writing style: Analytical, sophisticated, uses film theory
- Specialization: Drama, indie films, auteur cinema
- Tone: Professional but accessible
- Focus: Deep analysis, cinematography, director's vision

**Author 2: Enthusiastic Pop Culture Blogger**
- Writing style: Conversational, witty, pop culture references
- Specialization: Blockbusters, franchises, streaming content
- Tone: Casual, friendly, relatable
- Focus: Entertainment value, "worth watching?", streaming guides

**Author 3: Skeptical Genre Expert**
- Writing style: Critical, sarcastic, contrarian views
- Specialization: Horror, thriller, sci-fi
- Tone: Dark humor, honest, not afraid of unpopular opinions
- Focus: Genre analysis, hidden gems, controversial takes

Each author has avatar, bio, and detailed personality prompt stored in database.

### 3. Automated Nightly Pipeline

**23:00 - AI-Powered Scraping**
- System visits all active source websites
- Downloads HTML of each page
- Sends HTML to AI with prompt: "Extract all film-related articles from this page"
- AI returns structured data for each article found:
  - Title
  - Content summary (first 2-3 paragraphs)
  - Publication date
  - Author name
  - Featured image URL
  - Article URL
  - Type (news/review/interview/streaming)
- System validates and stores extracted articles in database
- **Advantage:** No CSS selectors needed, works on any website structure, resilient to design changes

**00:00 - Content Selection**
- AI analyzes all scraped articles from last 24 hours
- Selects 1-2 most interesting articles based on:
  - Topic diversity (avoid duplicates)
  - Trending films (check IMDb/ČSFD popularity)
  - Czech relevance (local release dates, streaming availability)
  - Recency
- Marks selected articles for generation

**01:00 - Article Generation**
For each selected article:

1. **Research Phase:**
   - Gather additional info from IMDb, ČSFD
   - Check streaming availability in Czech Republic
   - Find related films, director info, cast details
   - Collect fun facts and trivia

2. **Author Assignment:**
   - Match article topic with author specialization
   - For reviews: randomly decide if single author or roundtable (3 authors discussing)

3. **AI Content Generation:**
   - Send comprehensive prompt to premium AI model
   - Prompt includes: author personality, research data, Czech context requirements
   - AI generates 800-1200 word original article in Czech
   - Article must include:
     - Original perspective (not translation)
     - Czech context (where to watch, dubbing info, local release)
     - 1-2 fun facts or unique insights
     - Personal rating/recommendation
     - Natural, human-like writing in author's style

4. **Content Enhancement:**
   - Generate SEO meta description
   - Create URL slug from title
   - Download and optimize featured image
   - Extract/generate article tags
   - Add streaming platform information
   - Add "Zajímavost" (fun fact) section

5. **Special Format - Roundtable Reviews:**
   - For some reviews, generate multi-author format
   - Each of 3 authors gives their perspective (200-300 words)
   - Show disagreements and different viewpoints
   - Makes content feel more authentic and diverse

**02:00 - Quality Validation**
Automated checks before publishing:
- Minimum 600 words
- Language is Czech (not accidentally English)
- Has featured image
- Plagiarism check (must be <30% similar to source)
- Has at least 3 tags
- All metadata present

If passes all checks: publish immediately
If fails: save as draft and log for manual review

---

## Database Schema

### sources
- Source website information
- No CSS selectors stored
- Just basic info: name, URL, type, language

### scraped_articles
- Articles extracted by AI from sources
- Stores: title, content summary, author, date, image URL, original URL
- Includes HTML snapshot for reference
- Tracks processing status

### articles
- Final generated articles ready for publishing
- Complete content, metadata, SEO data
- References to source articles used
- Author assignment, publication date, view count

### article_metadata
- Extended info: IMDb ID, ČSFD URL, genres
- Czech release date
- Streaming platform availability
- Fun facts

### authors
- 3 fictional author profiles
- Detailed personality descriptions
- Writing style prompts for AI
- Avatar, bio, specialization

### tags
- Article tagging system
- Genre, actor names, directors, themes

---

## Frontend Design

### Homepage
- **Hero Section:** Latest featured article with large image
- **Grid Layout:** 3 columns on desktop, 1 on mobile
- **Article Cards:** Each shows image, title, author avatar, date, short excerpt
- **Sidebar:** Popular articles, author profiles, category filters
- **Dark/Light Mode:** Toggle option

### Article Page Layout

**Header:**
- Large featured image (full width)
- Article category badge
- Main title (large, bold)
- Author info: avatar, name, date, read time

**Main Content:**
- Article text in readable typography
- Clean paragraph spacing
- Proper heading hierarchy

**"Kde sledovat" Section:**
- Highlighted box with streaming platform badges
- Clickable buttons linking to platforms (affiliate links)
- Czech release date info

**"Zajímavost" Box:**
- Special highlighted section with fun fact
- Distinctive styling (colored border or background)

**Bottom Elements:**
- Article tags (clickable)
- Share buttons
- Related articles (3 suggestions)

**Design Principles:**
- Modern, clean aesthetic
- Strong use of imagery
- Readable typography
- Smooth hover effects and transitions
- Mobile-first responsive design

### Livewire Components Needed
- ArticleCard (for grids)
- ArticleGrid (with filtering)
- SearchBar (with live results)
- CategoryFilter
- AuthorProfile display

---

## SEO & Monetization

### SEO Features
- Auto-generated sitemap (updates daily)
- Schema.org Article markup
- Open Graph tags for social sharing
- Twitter Card tags
- Canonical URLs
- Optimized images with alt tags
- Internal linking between related articles

### URL Structure
- `/{slug}` - Main articles
- `/recenze/{slug}` - Reviews
- `/novinky/{slug}` - News
- `/pruvodce/{slug}` - Guides
- `/autor/{author-slug}` - Author pages
- `/tag/{tag-slug}` - Tag pages

### Monetization
**Google AdSense:**
- Ad placement after 2-3 paragraphs in articles
- Sidebar ads
- Between article cards on homepage
- Follow ad density guidelines

**Affiliate Links:**
- Streaming platform buttons (Netflix, HBO Max, Disney+, etc.)
- Affiliate tracking for clicks
- Clean, non-intrusive placement

---

## Monitoring Dashboard

Simple password-protected page at `/monitoring`

**Display:**
- Last 20 generated articles (title, status, author, date)
- Scraping status (last run time, success/failure)
- AI API usage statistics (tokens used, estimated cost)
- Article performance (total published, views, top 10)
- Error log (last 50 errors)

**Manual Actions Available:**
- Publish draft articles manually
- Trigger scraping on demand
- Test extraction on custom URL
- Regenerate specific article

---

## Laravel Commands

### Scraping
- `scrape:sources` - Run all active sources
- `scrape:single {source_id}` - Test single source
- `scrape:test {url}` - Test AI extraction on any URL

### Content Generation
- `content:select` - Select articles for generation
- `content:generate` - Generate all selected articles
- `content:generate-single {id}` - Generate one specific article

### Validation & Maintenance
- `content:validate` - Validate pending articles
- `content:cleanup-old-scraped` - Delete old scraped data (30+ days)
- `sitemap:generate` - Regenerate sitemap
- `images:optimize` - Optimize all images

### Testing
- `test:author-style {author_id} {topic}` - Test author writing style
- `test:extraction {url}` - Debug extraction on URL
- `test:full-pipeline` - Test entire pipeline end-to-end

---

## Automated Schedule

**Daily Tasks:**
- 23:00 - Scrape sources
- 00:00 - Select content
- 01:00 - Generate articles (can take up to 1 hour)
- 02:00 - Validate and publish
- Daily (anytime) - Regenerate sitemap

**Weekly Tasks:**
- Sunday 03:00 - Cleanup old scraped articles
- Sunday 04:00 - Optimize images

---

## Configuration Settings

**Scraping Settings:**
- User agent rotation (multiple agents)
- Timeout: 30 seconds per request
- Delay between requests: 3-5 seconds
- Max articles per source: 10 per run

**AI Settings:**
- Extraction model: GPT-3.5-turbo (cheap)
- Generation model: GPT-4-turbo (premium)
- Temperature: 0.3 for extraction (precise), 0.8 for generation (creative)
- Daily spending limit: 50 USD

**Content Settings:**
- Articles generated per night: randomly 1-2
- Minimum word count: 600 words
- Maximum word count: 1500 words
- Roundtable probability: 30% for reviews

**Quality Thresholds:**
- Plagiarism threshold: <30% similarity to source
- Minimum uniqueness score: 70%

---

## Implementation Phases

**Phase 1: Core Infrastructure (Week 1)**
- Laravel setup with all packages
- Database schema and migrations
- Basic models (Source, Article, Author, etc.)
- AI service integration classes

**Phase 2: Scraping & Extraction (Week 1-2)**
- Implement AI-powered extraction
- Test on 3-5 real websites
- Error handling and logging
- Validation of extracted data

**Phase 3: Content Generation (Week 2)**
- Author personas in database
- Research data gathering (IMDb, ČSFD)
- AI generation pipeline
- Quality validation system

**Phase 4: Automation (Week 2-3)**
- Laravel scheduler configuration
- All cron jobs setup
- End-to-end pipeline testing
- Error notifications (email alerts)

**Phase 5: Frontend (Week 3-4)**
- TailwindCSS base layout
- Homepage design
- Article page template
- Author and tag pages
- Livewire components
- Mobile responsiveness

**Phase 6: SEO & Monetization (Week 4)**
- SEO optimization
- Schema markup
- Sitemap generation
- AdSense integration
- Affiliate link system

**Phase 7: Testing & Launch (Week 5)**
- Full pipeline testing
- Performance optimization
- Security review
- Monitoring setup
- Soft launch with careful monitoring

---

## Critical Requirements

### AI Extraction Intelligence
- AI must identify articles vs navigation/ads/comments
- Extract only relevant film content
- Handle different website structures automatically
- Works without any manual CSS selector configuration
- Resilient to website redesigns

### Content Originality
- **NEVER copy-paste from source articles**
- AI must completely rewrite in own words
- Add Czech context that wasn't in original
- Include personal perspective from author persona
- Minimum 70% uniqueness compared to source

### Czech Localization
Every article must include:
- Where to watch in Czech Republic (streaming platforms)
- Czech release date
- Information about Czech dubbing/subtitles
- Local context and relevance

### Quality Standards
- Natural, human-like writing
- No robotic or generic AI language
- Each author has consistent voice
- Grammatically correct Czech
- Engaging, not boring
- SEO-friendly but not keyword-stuffed

### Performance
- All heavy operations in queues
- Caching for article pages
- Optimized images (WebP format, lazy loading)
- Fast page loads (<2 seconds)

### Legal Compliance
- Store source URLs as references
- Transform content, don't copy
- Optional disclaimer about AI-generated content
- Respect robots.txt on source sites
- Rate limiting to avoid being blocked

### Cost Management
- Monitor AI API costs daily
- Alert if approaching spending limit
- Use cheap models for extraction
- Use premium models only for generation
- Cache where possible to avoid redundant calls

---

## Success Metrics

After 30 days of operation:
- 30-60 published articles
- All articles unique (pass plagiarism check)
- Zero manual intervention needed (fully automated)
- Fast page loads
- Mobile responsive
- SEO-optimized
- AdSense approved
- Monitoring dashboard working

---

## Key Advantages of This Approach

1. **Zero Maintenance:** No CSS selectors to update when sites redesign
2. **Scalable:** Add new sources by just adding URL to database
3. **Authentic:** 3 different writing styles make it feel like real blog
4. **Czech-Focused:** All content localized for Czech audience
5. **Fully Automated:** No daily management needed
6. **Cost-Effective:** Smart use of cheap vs premium AI models
7. **Quality Control:** Automated validation before publishing
8. **SEO-Optimized:** Built-in best practices
9. **Monetization-Ready:** AdSense and affiliate integration

---

This specification provides complete guidance for building the platform without requiring any manual content management or daily intervention. The system runs autonomously, generating and publishing quality Czech film content every night.