"use client"

import styles from "./ProposalDetailCard.module.css"
import Link from "next/link"

export default function ProposalDetailCard({ proposal }) {
  console.log(proposal)
  const getStatusClass = (status) => {
    switch (status) {
      case "submitted":
        return styles.statusSubmitted
      case "accepted":
        return styles.statusAccepted
      case "rejected":
        return styles.statusRejected
      default:
        return styles.statusDefault
    }
  }

  return (
    <div className={styles.card}>
      <div className={styles.header}>
        <h2 className={styles.title}>{proposal.title}</h2>
        <span className={`${styles.status} ${getStatusClass(proposal.status)}`}>{proposal.status}</span>
      </div>

      <div className={styles.section}>
        <h3 className={styles.sectionTitle}>Description</h3>
        <p className={styles.description}>{proposal.description}</p>
      </div>

      <div className={styles.section}>
        <h3 className={styles.sectionTitle}>Provider Information</h3>
        <div className={styles.info}>
          <div className={styles.infoItem}>
            <span className={styles.label}>Provider ID:</span>
            <span className={styles.value}>{proposal.provider_id}</span>
          </div>
          <div className={styles.infoItem}>
            <span className={styles.label}>Provider Name:</span>
            <Link href={`/profile/${proposal.provider_id}`} className={styles.value}>
              <span className={styles.value}>{proposal.provider_name}</span>
            </Link>
          </div>
        </div>
      </div>

      <div className={styles.section}>
        <h3 className={styles.sectionTitle}>Related Problem</h3>
        <div className={styles.problemCard}>
          <div className={styles.infoItem}>
            <span className={styles.label}>Problem ID:</span>
            <span className={styles.value}>{proposal.problem.id}</span>
          </div>
          <div className={styles.infoItem}>
            <span className={styles.label}>Problem Title:</span>
            <span className={styles.value}>{proposal.problem.title}</span>
          </div>
          <div className={styles.infoItem}>
            <span className={styles.label}>Company ID:</span>
            <span className={styles.value}>{proposal.problem.company_id}</span>
          </div>
        </div>
      </div>

      <div className={styles.section}>
        <h3 className={styles.sectionTitle}>Timeline</h3>
        <div className={styles.info}>
          <div className={styles.infoItem}>
            <span className={styles.label}>Created:</span>
            <span className={styles.value}>{new Date(proposal.created_at).toLocaleString()}</span>
          </div>
          <div className={styles.infoItem}>
            <span className={styles.label}>Updated:</span>
            <span className={styles.value}>{new Date(proposal.updated_at).toLocaleString()}</span>
          </div>
        </div>
      </div>

      <div className={styles.section}>
        <h3 className={styles.sectionTitle}>Proposal Documents</h3>
        <div className={styles.info}>
          {proposal.docs && proposal.docs.length > 0 ? (
            <div className={styles.docs}>
              {proposal.docs.map((doc, index) => (
                <div key={doc.id} className={styles.docsItem}>
                  <span className={styles.docsLabel}>Proposal {index + 1}:</span>
                  <span className={styles.docsValue}>
                    <Link href={`${process.env.NEXT_PUBLIC_BASE_URL}${doc.file_url}`} target="_blank">
                      File {index + 1}
                    </Link>
                  </span>
                </div>
              ))}
            </div>
          ) : (
            <p className={styles.noDocs}>No docs found.</p>
          )}
        </div>
      </div>
    </div>
  )
}
