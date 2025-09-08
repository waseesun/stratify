"use client"

import { useState } from "react"
import styles from "./page.module.css"

export default function HomePage() {
  const [isMenuOpen, setIsMenuOpen] = useState(false)

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen)
  }

  return (
    <div className={styles.container}>
      {/* Hero Section */}
      <section className={styles.hero}>
        <div className={styles.heroContent}>
          <h1 className={styles.heroTitle}>
            Simplify Your Project Management with <span>Stratify</span>
          </h1>
          <p className={styles.heroSubtitle}>
            Streamline problems, proposals, projects, transactions, and notifications all in one powerful platform.
            Built for teams that value efficiency and clarity.
          </p>
          <div className={styles.heroActions}>
            <button className={styles.primaryButton}>Explore Features</button>
            <button className={styles.secondaryButton}>Watch Demo</button>
          </div>
        </div>
        <div className={styles.heroVisual}>
          <div className={styles.dashboardPreview}>
            <div className={styles.previewCard}>
              <div className={styles.cardHeader}></div>
              <div className={styles.cardContent}>
                <div className={styles.contentLine}></div>
                <div className={styles.contentLine}></div>
                <div className={styles.contentLine}></div>
              </div>
            </div>
            <div className={styles.previewCard}>
              <div className={styles.cardHeader}></div>
              <div className={styles.cardContent}>
                <div className={styles.contentLine}></div>
                <div className={styles.contentLine}></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className={styles.features}>
        <div className={styles.featuresContent}>
          <h2 className={styles.sectionTitle}>Everything You Need to Succeed</h2>
          <p className={styles.sectionSubtitle}>
            Powerful tools designed to help you manage every aspect of your projects
          </p>
          <div className={styles.featuresGrid}>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="12" cy="12" r="3" />
                  <path d="M12 1v6m0 6v6" />
                  <path d="m21 12-6 0m-6 0-6 0" />
                </svg>
              </div>
              <h3>Problem Management</h3>
              <p>Identify, track, and resolve issues efficiently with our comprehensive problem management system.</p>
            </div>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10,9 9,9 8,9" />
                </svg>
              </div>
              <h3>Smart Proposals</h3>
              <p>Create, review, and approve proposals with intelligent workflows and automated notifications.</p>
            </div>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22,4 12,14.01 9,11.01" />
                </svg>
              </div>
              <h3>Project Tracking</h3>
              <p>Monitor project progress, deadlines, and deliverables with real-time updates and insights.</p>
            </div>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <line x1="12" y1="1" x2="12" y2="23" />
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
              </div>
              <h3>Transaction Management</h3>
              <p>Handle payments, invoices, and financial records with secure and transparent processes.</p>
            </div>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                  <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                </svg>
              </div>
              <h3>Smart Notifications</h3>
              <p>Stay informed with intelligent alerts and updates tailored to your workflow preferences.</p>
            </div>
            <div className={styles.featureCard}>
              <div className={styles.featureIcon}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4" />
                  <polyline points="9,11 12,14 15,11" />
                  <line x1="12" y1="2" x2="12" y2="14" />
                </svg>
              </div>
              <h3>Secure Integration</h3>
              <p>Connect with your favorite tools and platforms through our secure API and integration system.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className={styles.testimonials}>
        <div className={styles.testimonialsContent}>
          <h2 className={styles.sectionTitle}>Trusted by Teams Worldwide</h2>
          <div className={styles.testimonialsGrid}>
            <div className={styles.testimonialCard}>
              <div className={styles.testimonialContent}>
                <p>
                  "Stratify transformed how we manage our projects. The intuitive interface and powerful features have
                  increased our team's productivity by 40%."
                </p>
              </div>
              <div className={styles.testimonialAuthor}>
                <div className={styles.authorAvatar}></div>
                <div className={styles.authorInfo}>
                  <h4>Sarah Johnson</h4>
                  <p>Project Manager, TechCorp</p>
                </div>
              </div>
            </div>
            <div className={styles.testimonialCard}>
              <div className={styles.testimonialContent}>
                <p>
                  "The proposal management system is a game-changer. We can now track everything from initial submission
                  to final approval seamlessly."
                </p>
              </div>
              <div className={styles.testimonialAuthor}>
                <div className={styles.authorAvatar}></div>
                <div className={styles.authorInfo}>
                  <h4>Michael Chen</h4>
                  <p>Operations Director, InnovateLab</p>
                </div>
              </div>
            </div>
            <div className={styles.testimonialCard}>
              <div className={styles.testimonialContent}>
                <p>
                  "Stratify's notification system keeps our entire team aligned. No more missed deadlines or forgotten
                  tasks. It's simply brilliant."
                </p>
              </div>
              <div className={styles.testimonialAuthor}>
                <div className={styles.authorAvatar}></div>
                <div className={styles.authorInfo}>
                  <h4>Emily Rodriguez</h4>
                  <p>Team Lead, CreativeStudio</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className={styles.cta}>
        <div className={styles.ctaContent}>
          <h2>Ready to Stratify Your Success?</h2>
          <p>Join thousands of teams who have already transformed their project management workflow.</p>
          <div className={styles.ctaActions}>
            <button className={styles.primaryButton}>Start Free Trial</button>
            <button className={styles.secondaryButton}>Schedule Demo</button>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className={styles.footer}>
        <div className={styles.footerContent}>
          <div className={styles.footerSection}>
            <h3>Stratify</h3>
            <p>Simplifying project management for teams worldwide.</p>
          </div>
          <div className={styles.footerSection}>
            <h4>Product</h4>
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <a href="#integrations">Integrations</a>
            <a href="#api">API</a>
          </div>
          <div className={styles.footerSection}>
            <h4>Company</h4>
            <a href="#about">About Us</a>
            <a href="#careers">Careers</a>
            <a href="#blog">Blog</a>
            <a href="#contact">Contact</a>
          </div>
          <div className={styles.footerSection}>
            <h4>Support</h4>
            <a href="#help">Help Center</a>
            <a href="#privacy">Privacy Policy</a>
            <a href="#terms">Terms of Service</a>
            <a href="#security">Security</a>
          </div>
        </div>
        <div className={styles.footerBottom}>
          <p>&copy; 2024 Stratify. All rights reserved.</p>
        </div>
      </footer>
    </div>
  )
}
