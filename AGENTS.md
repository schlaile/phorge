# Agent Instructions

This file is a local working note for internal agent use in this repository.
It is not intended to be proposed upstream as part of Phorge itself.

## Purpose

- remind local coding agents about repository-specific working rules
- preserve commit hygiene for patches that may later be proposed upstream
- keep local process notes out of user-facing code and out of upstream review

## Upstream Scope

- do not treat this file as upstream-facing project documentation
- if a change is prepared for upstream submission, this file should normally not
  be included in that submission
- when in doubt, prefer keeping agent/process instructions local

## Commit Message Rules

- write commit messages in English
- local planning for the PHP 8 compatibility effort is tracked in `T1371`
- commits created in this repository should avoid internal task references like
  `T1234` if the change may later be proposed upstream
- for PHP 8 compatibility work, mention `T1371` in local notes, patch exports,
  or review context, not in upstreamable commit messages in this repository
- for branches or commits intended only for local experimentation, internal
  references are acceptable if they help local tracking
- if it is unclear whether a change may later be reused upstream, default to a
  neutral commit message without internal task IDs

## Working Style

- keep changes small and reviewable
- prefer messages and diffs that can be reused in later upstream preparation
- do not add local process commentary to source files unless it belongs in code
  comments or documentation for human readers
