"use client"

import styles from "./AcceptProposalButton.module.css"

export default function AcceptProposalButton({ onClick }) {
  return (
    <button className={styles.button} onClick={onClick}>
      Accept Proposal
    </button>
  )
}
