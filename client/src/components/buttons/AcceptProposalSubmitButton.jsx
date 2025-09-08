import styles from "./AcceptProposalSubmitButton.module.css"

export default function AcceptProposalSubmitButton({ pending }) {
  return (
    <button type="submit" className={styles.button} disabled={pending}>
      {pending ? "Creating Project..." : "Accept Proposal"}
    </button>
  )
}
