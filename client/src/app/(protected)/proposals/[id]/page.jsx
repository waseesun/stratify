"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import { getUserIdAction } from "@/actions/authActions"
import { getProposalAction } from "@/actions/proposalActions"
import ProposalDetailCard from "@/components/cards/ProposalDetailCard"
import {UpdateProposalButton,
DeleteProposalButton} from "@/components/buttons/Buttons"
import styles from "./page.module.css"

export default function ProposalDetailPage() {
  const params = useParams()
  const [proposal, setProposal] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [userId, setUserId] = useState(null)

  const fetchUserID = async () => {
    const userId = await getUserIdAction();

    if (userId) {
      setUserId(userId);
    } else {
      router.push("/auth/login");
    }
  }

  const fetchProposal = async () => {
    setLoading(true)
    setError("")

    try {
      const result = await getProposalAction(params.id)

      if (result.error) {
        setError(result.error)
        setProposal(null)
      } else {
        setProposal(result.data)
      }
    } catch (err) {
      setError("Failed to fetch proposal")
      setProposal(null)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    if (params.id) {
      fetchUserID()
      fetchProposal()
    }
  }, [params.id])

  const refreshProposal = () => {
    fetchProposal()
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading proposal...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>{typeof error === "object" ? JSON.stringify(error) : error}</div>
      </div>
    )
  }

  if (!proposal) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Proposal not found</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Proposal Details</h1>
        {userId == proposal.provider_id && (
          <div className={styles.actions}>
            <UpdateProposalButton proposal={proposal} onSuccess={refreshProposal} />
            <DeleteProposalButton proposalId={proposal.id} />
          </div>
        )}
      </div>

      <ProposalDetailCard proposal={proposal} />
    </div>
  )
}
